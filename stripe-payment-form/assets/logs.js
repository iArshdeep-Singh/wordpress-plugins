(() => {

    const table = document.getElementById("payment-logs")
    const previous = document.getElementById("previous")
    const next = document.getElementById("next")
    let limit = Number(document.querySelector('select[name="limit"]').value)
    // let limit = 17
    let card = document.querySelector('select[name="type"]').value
    let offset = 0
    let sort_by = "created_at"
    let order = document.querySelector('select[name="order"]').value


    document.addEventListener('DOMContentLoaded', async () => {

        let { columns, rows, count } = await call_api(card, limit, offset, sort_by, order)


        console.log(columns)
        console.log(rows)

        create_sorting(columns, "created_at")

        let total = Number(count)

        if (limit >= total) {
            next.disabled = true
        }

        append_headers(columns)
        append_rows(rows)

        next.value = limit
        previous.value = 0
    })


    next.addEventListener('click', () => next_previous_button(event, card, limit, sort_by, order));
    previous.addEventListener('click', () => next_previous_button(event, card, limit, sort_by, order));

    // handle limit, type, sorting and order by
    document.querySelectorAll('.limit-type-sort-order').forEach(element => {

        element.addEventListener('change', async function () {

            console.log(offset, "-offset")

            if (this.name == "limit") {
                limit = Number(this.value)

                offset = 0
                next.value = limit
                previous.value = 0

                console.log(limit, "-limit")
            }
            if (this.name == "type") {
                card = this.value === "true"
                offset = 0
                next.value = limit
                previous.value = 0
                // console.log(card)
            }

            if (this.name == "sort_by") {
                sort_by = this.value
                offset = 0
                next.value = limit
                previous.value = 0
                // console.log(sort_by, "-sort_by")
            }

            if (this.name == "order") {
                order = this.value
                // console.log(order, "-order")
            }

            const { columns, rows, count } = await call_api(card, limit, offset, sort_by, order)

            // console.log(rows)
            console.log(count)

            create_sorting(columns, sort_by)
            append_headers(columns)
            append_rows(rows)

            if (limit + offset >= Number(count)) {
                next.disabled = true
                previous.disabled = true
                return
            }

            if (limit + offset < Number(count)) {
                next.disabled = false
                return
            }

            if (offset <= 0) {
                previous.disabled = true
                return
            }

            previous.disabled = false
            next.disabled = false
        })
    })



    // handle next-previous page
    async function next_previous_button(event, card, limit, sort_by, order) {

        offset = Number(event.target.value)

        // console.log(offset, "offset")

        let { columns, rows, count } = await call_api(card, limit, offset, sort_by, order)

        let total = Number(count)
        let updated_offset


        append_headers(columns)
        append_rows(rows)


        if (event.target.id === "previous") {

            if (offset <= 0) {
                event.target.disabled = true
                next.disabled = false
                next.value = limit
                return
            }


            updated_offset = offset - limit
            event.target.value = updated_offset
            next.value = offset + limit
        }


        if (event.target.id === "next") {

            if (offset + limit >= total) {
                event.target.disabled = true
                previous.disabled = false
                previous.value = offset - limit
                return
            }

            updated_offset = limit + offset
            event.target.value = updated_offset
            previous.value = offset - limit
        }

        console.log(Number(next.value), "next")
        console.log(Number(previous.value), "previous")

        previous.disabled = false
        next.disabled = false
    }

    function append_rows(rows) {

        rows.forEach(rows => {

            let tr = document.createElement('tr')

            for (key in rows) {
                let td = document.createElement('td')
                td.append(rows[key])
                tr.appendChild(td)
            }

            table.appendChild(tr)
        })
    }

    function append_headers(columns) {

        table.innerHTML = ""

        let header_row = document.createElement('tr')

        for (key in columns) {
            const th = document.createElement('th')
            // th.textContent = headers[key]
            th.append(columns[key])
            header_row.appendChild(th)
        }

        // Insert header as first row
        table.insertBefore(header_row, table.firstChild)


        // forEach() loop is always used for ARRAYS (cannot use 'break' and 'continue')
        // for...in() can be used for both ARRAYS and OBJECTS
        // append() can append DOM elements, text directly, and multiple values and returns 'undefined'
        // appendChild() appends only a single DOM element, doesn't append text, and the returns appended node
    }

    function create_sorting(columns, default_selected) {

        console.log(default_selected, "default")

        let select = document.getElementsByName("sort_by")[0]

        select.innerHTML = ""

        for (key in columns) {
            let opt = document.createElement("option")

            opt.textContent = columns[key]
            opt.value = key
            opt.selected = default_selected == key ? true : false

            select.appendChild(opt)
        }
    }

    async function call_api(card, limit, offset, sort_by, order) {
        const response = await fetch(logs_data.ajax_url + "?action=get_payment_logs", {
            headers: { "content-type": "application/json" },
            method: 'POST',
            body: JSON.stringify({ card: card, limit: limit, offset: offset, sort_by: sort_by, order: order })
        })

        const data = await response.json()

        return data
    }

})()