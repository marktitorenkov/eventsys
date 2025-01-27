function tabs_init(options) {
  options = Object.assign({}, {
    keys: null,
    panelSuffix: '-panel',
    btnSuffix: '-toggle',
    activeClass: 'active',
    inactiveClass: 'inactive',
    onClick: () => {},
  }, options)

  if (!options.keys) return;

  function switchTo(targetKey) {
    for (const key of options.keys) {
      const btn = document.getElementById(key+options.btnSuffix)
      const panel = document.getElementById(key+options.panelSuffix)
      if (targetKey === key) {
        panel.style.display = 'revert'
        btn.classList.add(options.activeClass)
        btn.classList.remove(options.inactiveClass)
      } else {
        panel.style.display = 'none'
        btn.classList.remove(options.activeClass)
        btn.classList.add(options.inactiveClass)
      }
    }
  }

  for (const key of options.keys) {
    const btn = document.getElementById(key+options.btnSuffix)
    btn.addEventListener('click', () => {
      if (!options.onClick(key)) {
        switchTo(key)
      }
    })
  }

  return switchTo
}
