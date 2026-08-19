<form id="form">
    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" required/>
    </div>
    <div class="form-group">
        <label for="amount">Amount</label>
        <input type="text" id="amount" required/>
    </div>
    <div class="form-submit">
        <button type="submit" onclick="pay()"> Pay</button>
    </div>
</form>

<script src="https://js.lahza.io/inline.min.js"></script>

<script>
    const paymentForm = document.getElementById('form');
    paymentForm.addEventListener("submit", pay, false);

    function pay(e) {
        e.preventDefault();
        const lahza = new LahzaPopup();
        lahza.newTransaction({
            key: "{{ \Illuminate\Support\Facades\Config::get('lahza.public_key') }}",
            email: document.getElementById("email").value,
            currency: "USD",
            amount: document.getElementById("amount").value * 100,
            onSuccess: (transaction) => {
                let message = 'Payment complete! Reference: ' + transaction.reference;
                alert(message);
            },
            onCancel: () => {
                alert('Window closed.');
            }
        });
    }
</script>
