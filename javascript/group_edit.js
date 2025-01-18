// Show/Hide element with given name
function toggleElementVisibility(element_name) {
  const elem = document.getElementById(element_name);
  elem.style.display = elem.style.display === 'none' ? 'block' : 'none';
}

// Delete Group Confirmation Process
function confirmGroupDelete() {
  toggleElementVisibility('delete-modal');
  toggleElementVisibility('btn-delete-group');
}


function toggleEditGroupPanel() {
  const editPanel = document.getElementById('group-edit-panel');
  const memberPanel = document.getElementById('group-members-panel');

  if (editPanel.style.display === 'none') {
    memberPanel.style.display = 'none';
    editPanel.style.display = 'block';
  }
}

function toggleGroupMembersPanel() {
  const editPanel = document.getElementById('group-edit-panel');
  const memberPanel = document.getElementById('group-members-panel');

  if (memberPanel.style.display === 'none') {
    editPanel.style.display = 'none';
    memberPanel.style.display = 'block';
  }
}