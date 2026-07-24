(() => {


    console.log(null == undefined, "Why?")


    let data = {}
    let pk = document.querySelector('.stripe-config input[name="pk"]')
    let sk = document.querySelector('.stripe-config input[name="sk"]')
    let amount = document.querySelector('.stripe-config input[placeholder="Enter Amount"]')
    let currency = document.querySelector('.stripe-config select[name="currency"]')
    let card_or_link = document.querySelector('.stripe-config select[name="card-or-link"]')
    let secure_link = document.querySelector('.stripe-config select[name="secure-link"]')
    let currency_amount_mode = document.querySelector('.stripe-config select[name="currency-amount-mode"]')


    document.addEventListener('DOMContentLoaded', () => {

        let currencies_select = currency

        currencies.forEach(currency => {
            let opt = document.createElement('option')

            opt.textContent = currency.code
            opt.value = currency.value
            opt.setAttribute("data-symbol", currency.symbol)
            opt.selected = currency.value == "usd" ? true : false

            currencies_select.appendChild(opt)
        })

    })


    document.querySelectorAll(".stripe-config").forEach((element) => {

        let select_tags = element.querySelector("select")

        if (select_tags && !element.querySelector('select[name="currency"]')) {

            select_tags.addEventListener('change', function () {

                switch (this.name) {
                    case "card-or-link":
                        data.card_or_link = this.value
                        break
                    case "secure-link":
                        data.secure_link = this.value
                        break
                    case "currency-amount-mode":
                        data.currency_amount_mode = this.value
                        if (this.value === "fixed") {
                            document.querySelector('.stripe-config[style="display: none;"]').style.display = "block"
                        } else {

                            if (document.querySelector('.stripe-config[style="display: block;"]')) {
                                data.amount = null
                                data.currency = null
                                document.querySelector('.stripe-config[style="display: block;"]').style.display = "none"
                            } else {
                                data.amount = null
                                data.currency = null
                            }
                        }
                        break
                    default:
                        console.log("Default")
                }
            })
        }
    })




    document.querySelectorAll(".stripe-config").forEach(element => {

        let input_tags = element.querySelector('input')


        if (input_tags && !element.style.display !== "none") {
            input_tags.addEventListener('input', function () {
                let span_tags = element.querySelector('span')
                switch (this.name) {
                    case "pk":
                        if (span_tags) {
                            this.style.borderColor = "black"
                            span_tags.textContent = ""
                        }
                        break
                    case "sk":
                        if (span_tags) {
                            this.style.borderColor = "black"
                            span_tags.textContent = ""
                        }
                        break
                    case "amount":
                        if (span_tags) {
                            this.style.borderColor = "black"
                            span_tags.textContent = ""
                        }
                        break
                    default:
                        console.log("Default")
                }
            })
        }
    })



    function input_validation() {
        let is_validate = true
        document.querySelectorAll(".stripe-config").forEach(element => {

            let input_tags = element.querySelector('input')

            if (input_tags && element.style.display !== "none") {

                switch (input_tags.name) {
                    case "pk":
                        if (!/^(pk_)/.test(input_tags.value)) {
                            if (input_tags.value == "") {
                                create_error_message(element, "Please this field.")
                                is_validate = false
                            } else {
                                create_error_message(element, "Invalid match.")
                                is_validate = false
                            }
                            break
                        }
                        break
                    case "sk":
                        if (!/^(sk_)/.test(input_tags.value)) {
                            if (input_tags.value == "") {
                                create_error_message(element, "Please this field.")
                                is_validate = false
                            } else {
                                create_error_message(element, "Invalid match.")
                                is_validate = false
                            }
                            break
                        }
                        break
                    case "amount":
                        if (Number(input_tags.value) < 0 || isNaN(Number(input_tags.value))) {
                            create_error_message(element, "Invalid Amount.")
                            is_validate = false
                            break
                        }
                        if (Number(input_tags.value) == 0) {
                            create_error_message(element, "Please this field.")
                            is_validate = false
                        }
                        break
                    default:
                        console.log("Default")
                }
            }

            function create_error_message(element, message) {

                if (element.querySelector('span')) {
                    element.querySelector('span').remove()
                }

                let error_message = document.createElement('span')
                input_tags.style.borderColor = "red"
                error_message.style.color = "red"
                error_message.textContent = message
                element.appendChild(error_message)
            }
        })

        return is_validate
    }


    document.getElementById('save-update-code').addEventListener('click', async () => {

        let is_validate = input_validation()

        if (!is_validate) {
            return
        }

        data.pk = pk.value
        data.sk = sk.value
        data.amount = Number(amount.value)
        data.currency = currency.value
        data.card_or_link = card_or_link.value
        data.secure_link = secure_link.value
        data.currency_amount_mode = currency_amount_mode.value

        let response = await fetch(settings_data.settings_url + "?action=payment_settings", {
            headers: { "content-type": "application/json" },
            method: "POST",
            body: JSON.stringify(data)
        })

        let parsed_data = await response.json()

        console.log(parsed_data)
    })

})()