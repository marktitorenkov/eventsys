(function(){
  const STORAGE_KEY = 'last-panel'
  const KEYS = ['group-edit', 'group-members', 'group-add']
  const swicthToPanel = tabs_init({keys: KEYS, onClick: (key) => {
    localStorage.setItem(STORAGE_KEY, key)}
  })

  // Preserve last open Panel on page reload
  const lastPanel = localStorage.getItem(STORAGE_KEY) || PANEL_KEYS[0]
  swicthToPanel(lastPanel)
})()


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
