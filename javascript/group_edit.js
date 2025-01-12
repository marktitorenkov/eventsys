document.addEventListener("DOMContentLoaded", () => {
    // get resources
    let deleteGroupBtn = document.getElementById('btn-delete-group');

    // add confirmation popup
    deleteGroupBtn.addEventListener('click', (event) => {
        let confirm_delete = window.confirm('Are you sure you want to DELETE this group?');

        if (confirm_delete == false) {
            event.preventDefault();
        }
    });
});