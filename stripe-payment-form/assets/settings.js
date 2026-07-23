(() => {


    let data = {}
    let pk = document.querySelector('.stripe-config input[name="pk"]')
    let sk = document.querySelector('.stripe-config input[name="sk"]')

    console.log(pk)
    console.log(k)

    document.addEventListener('DOMContentLoaded', () => {

        let currencies_select = document.querySelector('.stripe-config select[name="currency"]')

        currencies.forEach(currency => {
            let opt = document.createElement('option')

            opt.textContent = currency.code
            opt.value = currency.value
            opt.setAttribute("data-symbol", currency.symbol)
            opt.selected = currency.value == "usd" ? true : false

            currencies_select.appendChild(opt)
        })
    })




    data.pk = pk.value
    data.sk = sk.value

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
                    case "currency_amount_mode":
                        data.stripe_config = this.value
                        if (this.value === "fixed") {

                            data.amount = document.querySelector('.stripe-config input[placeholder="Enter Amount"]').value
                            document.querySelector('.stripe-config select[name="currency"]').addEventListener('change', function () {
                                data.currency = this.value
                            })
                        }
                        break
                    default:
                        console.log("Default")
                }

                console.log(data)
            })
        }
    })



    let currency_amount_mode = document.querySelector('.stripe-config select[name="currency_amount_mode"]')

    currency_amount_mode.addEventListener('change', function () {
        if (this.value == "fixed") {
            document.querySelectorAll('.stripe-config')[5].style.display = "block"
        } else {
            document.querySelectorAll('.stripe-config')[5].style.display = "none"
        }
    })





})()