<?php

use App\Models\User;
use App\Models\Transaction;

// Test to verify users can see the edit form for a transaction
test('authenticated user can view the edit transaction form', function () {
    // Create a user with a transaction
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'description' => 'Coffee',
        'amount' => 5.00,
        'type' => 'expense',
        'category' => 'Food',
    ]);

    // Act as the user and navigate to the edit page
    $response = $this->actingAs($user)->get(route('transactions.edit', $transaction->id));

    // Assert the page loads successfully and contains the transaction data
    $response->assertStatus(200);
    $response->assertSee('Edit Transaction');
    $response->assertSee('Coffee');
});

// Test to verify users can update a transaction
test('authenticated user can update their transaction', function () {
    // Create a user with a transaction
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'description' => 'Lunch',
        'amount' => 10.00,
        'type' => 'expense',
        'category' => 'Food',
    ]);

    // Act as the user and submit the update form
    $response = $this->actingAs($user)->put(route('transactions.update', $transaction->id), [
        'description' => 'Dinner',
        'amount' => 15.00,
        'type' => 'expense',
        'category' => 'Leisure',
        'entry_date' => $transaction->entry_date,
    ]);

    // Assert the redirect happens and the database is updated
    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'description' => 'Dinner',
        'amount' => 15.00,
        'category' => 'Leisure',
    ]);
});

// Test to verify users cannot update another user's transaction
test('user cannot update another user\'s transaction', function () {
    // Create two different users
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create a transaction for user1
    $transaction = Transaction::factory()->create([
        'user_id' => $user1->id,
        'description' => 'User1 Expense',
        'amount' => 10.00,
    ]);

    // Try to update as user2 (should be forbidden)
    $response = $this->actingAs($user2)->put(route('transactions.update', $transaction->id), [
        'description' => 'Hacked!',
        'amount' => 999.00,
        'type' => 'expense',
        'category' => 'Food',
        'entry_date' => $transaction->entry_date,
    ]);

    // Assert we get a forbidden error
    $response->assertStatus(403);

    // Verify the database was NOT changed
    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'description' => 'User1 Expense',
        'amount' => 10.00,
    ]);
});

// Test to verify users can delete their transaction
test('authenticated user can delete their transaction', function () {
    // Create a user with a transaction
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'description' => 'Coffee',
        'amount' => 5.00,
    ]);

    // Act as the user and delete the transaction
    $response = $this->actingAs($user)->delete(route('transactions.destroy', $transaction->id));

    // Assert the redirect and the database record is deleted
    $response->assertRedirect();
    $this->assertDatabaseMissing('transactions', [
        'id' => $transaction->id,
    ]);
});

// Test to verify users cannot delete another user's transaction
test('user cannot delete another user\'s transaction', function () {
    // Create two different users
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create a transaction for user1
    $transaction = Transaction::factory()->create([
        'user_id' => $user1->id,
        'description' => 'User1 Expense',
        'amount' => 10.00,
    ]);

    // Try to delete as user2 (should be forbidden)
    $response = $this->actingAs($user2)->delete(route('transactions.destroy', $transaction->id));

    // Assert we get a forbidden error
    $response->assertStatus(403);

    // Verify the database still has the transaction
    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
    ]);
});

// Test to verify validation errors when updating with invalid data
test('update fails with invalid transaction data', function () {
    // Create a user with a transaction
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'description' => 'Coffee',
        'amount' => 5.00,
    ]);

    // Try to update with invalid data (empty description, negative amount, invalid type)
    $response = $this->actingAs($user)->put(route('transactions.update', $transaction->id), [
        'description' => '',  // Empty - should fail
        'amount' => -10,      // Negative - should fail
        'type' => 'invalid',  // Invalid type - should fail
        'category' => 'Food',
        'entry_date' => $transaction->entry_date,
    ]);

    // Assert validation fails and we have errors
    $response->assertSessionHasErrors(['description', 'amount', 'type']);
});

// New: verify dashboard provides correct category breakdown data
test('dashboard returns correct summary and breakdown arrays', function () {
    $user = User::factory()->create();

    // create mixed transactions
    Transaction::factory()->create([ 'user_id' => $user->id, 'type' => 'income', 'category' => 'Salary', 'amount' => 100 ]);
    Transaction::factory()->create([ 'user_id' => $user->id, 'type' => 'expense', 'category' => 'Food', 'amount' => 30 ]);
    Transaction::factory()->create([ 'user_id' => $user->id, 'type' => 'expense', 'category' => 'Food', 'amount' => 20 ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->has('summary.income')
        ->where('summary.income', 100)
        ->has('summary.expense')
        ->where('summary.expense', 50)
        ->has('summary.balance')
        ->where('summary.balance', 50)
        ->has('categories')
        ->etc()
    );
});
