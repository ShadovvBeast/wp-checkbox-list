document.addEventListener('DOMContentLoaded', function() {
    async function checkItem(item_id) {
        await (await fetch(ajax_object.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Cache-Control': 'no-cache',
            },
            body: "action=list_checkbox_check&item_id=" + item_id
        })).text();
    }

    document.querySelectorAll('.wp-checkbox-list-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', async event => {
                checkbox.disabled = true;
                await checkItem(checkbox.id);
        });
    });
});