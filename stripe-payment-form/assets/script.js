const stripe = Stripe(stripe_data.pk)

let elements

document.getElementById('load-payment').addEventListener('click', async function () {

    const amount = Math.round(parseFloat(document.getElementById('amount').value) * 100)

    if (amount <= 0) {
        alert("Enter valid amount")
        return
    }

    const response = await fetch(stripe_data.endpoint, {
        method: 'POST',
        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify({
            amount: amount
        })
    })

    const data = await response.json()

    // console.log(data)

    if (data.error) {
        document.getElementById("message").innerHTML = data.error.message
        return
    }

    elements = stripe.elements({ clientSecret: data.client_secret })

    const paymentElement = elements.create("payment", { layout: "tabs", wallets: { link: "never"} })

    console.log(paymentElement)

    paymentElement.mount("#payment-element")  // Here create() and mount() are functions by stripe (not JavaScript's built-in)

    document.getElementById("payment-button").style.display = "block"
})

document.getElementById('payment-button').addEventListener('click', async function () {
    const { error, paymentIntent } = await stripe.confirmPayment({ elements, redirect: "if_required" })

    if (error) {
        document.getElementById("message").innerHTML = error.message
    } else {
        if (paymentIntent.status === 'succeeded') {
            document.getElementById("message").style.color = "green"
            document.getElementById("message").innerHTML = "Payment Successful"
        }
    }
})

