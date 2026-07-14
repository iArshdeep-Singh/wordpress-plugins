(async () => {
    const stripe = Stripe(stripe_data.pk)


    let elements
    let paymentElement

    const amount = document.getElementById('amount')
    const proceed_button = document.getElementById('load-payment')
    const currency_select = document.getElementById("currency")
    const submitted_amount = document.getElementById('submitted-amount')

    const pay_button = document.getElementById("payment-button")
    const cancel_button = document.getElementById("cancel-button")
    const success_message = document.getElementById("success-message")
    const error_message = document.getElementById("error-message")


    let data = null

    if (amount.value > 0) {


        const enteredAmount = parseFloat(Number(amount.value)) * 100
        const [whole, cents] = parseFloat(amount.value).toFixed(2).split(".")


        const currency = currency_select.value;
        const symbol = currency_select.options[currency_select.selectedIndex].dataset.symbol

        await fetch(stripe_data.endpoint, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({
                amount: enteredAmount,
                currency: "usd"
            })
        }).then(async (res) => await res.json()).then((data) => {

            console.log(data)



            amount.style.display = "none"
            proceed_button.style.display = "none"
            currency_select.style.display = "none"
            cancel_button.style.display = "none"

            document.querySelector(".stripe-payment-wrap").style.display = "block"
            document.querySelector(".stripe-payment-wrap label").style.display = "none"

            submitted_amount.innerHTML = `<span class="symbol">${symbol}</span>
                                  <span class="whole">${whole}</span>
                                  <span class="cents">${cents}</span>`

            elements = stripe.elements({ clientSecret: data.client_secret })
            paymentElement = elements.create("payment", { layout: "tabs", wallets: { link: "never" } })

            paymentElement.mount("#payment-element")

            pay_button.style.display = "block"
        })



    } else {

        document.querySelector(".stripe-payment-wrap").querySelector("label").style.display = "block"
        document.querySelector(".stripe-payment-wrap").style.display = "block"

        proceed_button.addEventListener('click', async function () {


            const enteredAmount = parseFloat(Number(amount.value)) * 100
            const [whole, cents] = parseFloat(amount.value).toFixed(2).split(".")


            const currency = currency_select.value;
            const symbol = currency_select.options[currency_select.selectedIndex].dataset.symbol

            error_message.innerHTML = ""
            proceed_button.disabled = true

            console.log(enteredAmount)


            if (isNaN(enteredAmount) || enteredAmount <= 0) {
                alert("Enter valid amount")
                proceed_button.disabled = false
                return
            }


            const response = await fetch(stripe_data.endpoint, {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    amount: enteredAmount,
                    currency: currency
                })
            })

            data = await response.json()


            if (data.error) {
                proceed_button.disabled = false
                error_message.innerHTML = data.error.message
                return
            }

            amount.style.display = "none"
            proceed_button.style.display = "none"
            currency_select.style.display = "none"
            // document.getElementsByClassName("stripe-payment-wrap")[0].getElementsByTagName("label")[0].style.display = "none"
            // document.querySelector(".stripe-payment-wrap").querySelector("label").style.display = "none"
            document.querySelectorAll(".stripe-payment-wrap")[0].querySelectorAll("label")[0].style.display = "none"
            submitted_amount.innerHTML = `<span class="symbol">${symbol}</span>
                                  <span class="whole">${whole}</span>
                                  <span class="cents">${cents}</span>`

            elements = stripe.elements({ clientSecret: data.client_secret })

            paymentElement = elements.create("payment", { layout: "tabs", wallets: { link: "never" } })

            // console.log(paymentElement)

            paymentElement.mount("#payment-element")  // Here create() and mount() are functions by stripe (not JavaScript's built-in)

            pay_button.style.display = "block"
            cancel_button.style.display = "block"

            // console.log(data)
        })
    }





    // pay button
    pay_button.addEventListener('click', async function () {
        pay_button.disabled = true
        const { error, paymentIntent } = await stripe.confirmPayment({ elements, redirect: "if_required", confirmParams: { return_url: stripe_data.save_payment } })

        if (error) {
            pay_button.disabled = false
            error_message.innerHTML = error.message
        } else {
            if (paymentIntent.status === 'succeeded') {

                console.log(paymentIntent)

                await fetch(stripe_data.save_payment, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ payment_intent: paymentIntent?.id })
                }).then(async (response) => await response.json()).then(data => console.log(data))


                paymentElement.unmount()
                submitted_amount.style.display = "none"
                pay_button.style.display = "none"
                cancel_button.style.display = "none"
                amount.style.display = "none"
                amount.value = ""
                proceed_button.style.display = "none"
                proceed_button.disabled = false
                currency_select.style.display = "none"
                document.querySelector(".stripe-payment-wrap").querySelector("label").style.display = "none"
                error_message.innerHTML = ""
                success_message.style.color = "green"
                success_message.innerHTML = "Payment Successful"
            }
        }
    })



    // cancel button
    cancel_button.addEventListener('click', async function () {
        paymentElement.unmount()
        pay_button.style.display = "none"
        proceed_button.disabled = false
        cancel_button.style.display = "none"
        amount.style.display = "block"
        amount.value = ""
        proceed_button.style.display = "block"
        proceed_button.disabled = false
        currency_select.style.display = "block"
        document.querySelector(".stripe-payment-wrap").querySelector("label").style.display = "block"
        submitted_amount.innerHTML = ""
        error_message.innerHTML = ""
        success_message.innerHTML = ""
    })
})()