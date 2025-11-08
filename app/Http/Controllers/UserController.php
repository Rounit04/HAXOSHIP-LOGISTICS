<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\BookingCategory;
use App\Models\Notification;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Show user dashboard
     */
    public function dashboard()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your dashboard.');
        }

        $user = Auth::user();
        
        // Get user's support tickets
        $supportTickets = SupportTicket::where('user_id', $user->id)
            ->latest()
            ->get();
        
        // Get support type categories
        $supportCategories = BookingCategory::where('type', 'support')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        // Get user's notifications
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();
        
        $unreadNotificationsCount = Notification::getUnreadCountForUser($user->id);

        return view('user.dashboard', [
            'user' => $user,
            'supportTickets' => $supportTickets,
            'supportCategories' => $supportCategories,
            'notifications' => $notifications,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }

    /**
     * Store a new support ticket
     */
    public function storeSupportTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'category' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachments.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max per file
        ]);

        $user = Auth::user();

        // Create support ticket
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('support_tickets', $fileName, 'public');

                $ticket->attachments()->create([
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Support ticket created successfully! We will get back to you soon.');
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Show wallet page
     */
    public function wallet()
    {
        $user = Auth::user();
        $wallet = Wallet::getOrCreateWallet($user->id);
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(20);
        
        return view('user.wallet.index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show deposit form
     */
    public function walletDeposit()
    {
        $user = Auth::user();
        $wallet = Wallet::getOrCreateWallet($user->id);
        
        return view('user.wallet.deposit', [
            'wallet' => $wallet,
        ]);
    }

    /**
     * Process deposit
     */
    public function walletDepositStore(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $wallet = Wallet::getOrCreateWallet($user->id);
        
        try {
            $referenceId = 'DEP-' . time() . '-' . $user->id . '-' . Str::random(6);
            $wallet->deposit(
                $request->amount,
                $request->description ?? 'Wallet deposit',
                $referenceId
            );
            
            return redirect()->route('user.wallet')
                ->with('success', 'Deposit successful! Amount has been added to your wallet.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error processing deposit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show withdrawal form
     */
    public function walletWithdraw()
    {
        $user = Auth::user();
        $wallet = Wallet::getOrCreateWallet($user->id);
        $pendingWithdrawals = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->latest()
            ->get();
        
        return view('user.wallet.withdraw', [
            'wallet' => $wallet,
            'pendingWithdrawals' => $pendingWithdrawals,
        ]);
    }

    /**
     * Process withdrawal request
     */
    public function walletWithdrawStore(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $wallet = Wallet::getOrCreateWallet($user->id);
        
        try {
            if ($wallet->balance < $request->amount) {
                return redirect()->back()
                    ->with('error', 'Insufficient wallet balance.')
                    ->withInput();
            }

            $wallet->requestWithdrawal(
                $request->amount,
                $request->description ?? 'Withdrawal request'
            );
            
            return redirect()->route('user.wallet')
                ->with('success', 'Withdrawal request submitted successfully! It will be processed by admin.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error processing withdrawal: ' . $e->getMessage())
                ->withInput();
        }
    }
}

