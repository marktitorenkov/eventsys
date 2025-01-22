(function() {
  const toggleBtn = document.getElementById('toggle_expand')
  if (toggleBtn) {
    const ul = document.querySelector('.date-list')
    const classHide = 'hide'

    function update() {
      if (getExpand()) {
        toggleBtn.textContent = 'Show favorites'
        ul.classList.remove(classHide)
      } else {
        toggleBtn.textContent = 'Show all'
        ul.classList.add(classHide)
      }
    }

    let [getExpand, setExpand] = (function(){
      const key = 'events_expand'
      return [
        () => JSON.parse(localStorage.getItem(key)),
        (val) => localStorage.setItem(key, JSON.stringify(val)),
      ]
    })()

    toggleBtn.addEventListener('click', () => {
      setExpand(!getExpand())
      update()
    })
    update()
  }
})()
