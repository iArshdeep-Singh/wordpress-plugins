(() => {

    const table = document.getElementById("payment-logs")
    const previous = document.getElementById("previous")
    const next = document.getElementById("next")
    let limit = 5

    // sessionStorage.setItem("offset", limit)

    document.addEventListener('DOMContentLoaded', async () => {
        const response = await fetch(logs_data.ajax_url + "?action=get_payment_logs", {
            headers: { "content-type": "application/json" },
            method: 'POST',
            body: JSON.stringify({ card: true, limit: limit, offset: 0 })
        })

        const log_data = await response.json()
        let { columns, rows } = log_data

        append_headers(columns)
        append_rows(rows)


        // let offset = Number(sessionStorage.getItem("offset"))

        next.value = limit
        previous.value = 0

        console.log(previous.value, "previous")
        console.log(next.value, "next")
    })

    next.addEventListener('click', next_previous_button);
    previous.addEventListener('click', next_previous_button);

    async function next_previous_button() {



        let offset = Number(this.value)

        const response = await fetch(logs_data.ajax_url + "?action=get_payment_logs", {
            headers: { "content-type": "application/json" },
            method: 'POST',
            body: JSON.stringify({ card: true, limit: limit, offset: offset })
        })

        const log_data = await response.json()
        let { columns, rows, count } = log_data

        let total = Number(count)
        let updated_offset


        if (this.id === "previous") {

            if (offset == 0) {
                return
            }

            updated_offset = offset - limit
            this.value = updated_offset
            next.value = offset
        }


        if (this.id === "next") {

            if (offset >= total) {
                return
            }

            updated_offset = limit + offset
            this.value = updated_offset
            previous.value = offset

        }



        console.log(previous.value, "previous")
        console.log(next.value, "next")



        append_headers(columns)
        append_rows(rows)

        console.log(log_data)

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

})()