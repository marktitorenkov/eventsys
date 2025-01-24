// Preserve last open Panel on page reload
(function () {
  panel_name = localStorage.getItem('last-panel');
  console.log(panel_name)
  
  switch (panel_name) {
    case 'group-edit-panel':
      changeToEditGroupPanel();
      break;
    
    case 'group-members-panel':
      changeToGroupMembersPanel();
      break;
    
    case 'group-add-panel':
      changeToAddMembersPanel();
      break;
  };
})();

// Hide/Show Edit Group Panel
function toggleEditGroupPanel(value) {
  const panel = document.getElementById('group-edit-panel');
  const btn = document.getElementById('btn-toggle-edit');

  if (value === 'on') { // show
    localStorage.setItem('last-panel', 'group-edit-panel');

    btn.classList.remove('inactive');
    panel.style.display = 'block';
  }

  if (value === 'off') {// hide
    btn.classList.add('inactive');
    panel.style.display = 'none';
  }
}

// Hide/Show Group Members Panel
function toggleGroupMembersPanel(value) {
  const panel = document.getElementById('group-members-panel');
  const btn = document.getElementById('btn-toggle-members');

  if (value === 'on') { // show
    localStorage.setItem('last-panel', 'group-members-panel');

    btn.classList.remove('inactive');
    panel.style.display = 'block';
  }

  if (value === 'off') {// hide
    btn.classList.add('inactive');
    panel.style.display = 'none';
  }
}

// Hide/Show Add Members Panel
function toggleAddMembersPanel(value) {
  const panel = document.getElementById('group-add-panel');
  const btn = document.getElementById('btn-toggle-add');

  if (value === 'on') { // show
    localStorage.setItem('last-panel', 'group-add-panel');

    btn.classList.remove('inactive');
    panel.style.display = 'block';
  }

  if (value === 'off') {// hide
    btn.classList.add('inactive');
    panel.style.display = 'none';
  }
}

// Shows 'Edit Group' and Hides 'Group Members' Panel
function changeToEditGroupPanel() {
  toggleEditGroupPanel('on');
  toggleGroupMembersPanel('off');
  toggleAddMembersPanel('off');
}

// Shows 'Edit Group' and Hides 'Group Members' Panel
function changeToGroupMembersPanel() {
  toggleEditGroupPanel('off');
  toggleGroupMembersPanel('on');
  toggleAddMembersPanel('off');
}

function changeToAddMembersPanel() {
  toggleEditGroupPanel('off');
  toggleGroupMembersPanel('off');
  toggleAddMembersPanel('on');
}


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
