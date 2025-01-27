function toggleModifyForm() {
  const form = document.getElementById('modifyForm');
  form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function toggleDeleteModal() {
  const modal = document.getElementById('deleteModal');
  modal.style.display = modal.style.display === 'none' ? 'block' : 'none';
}
