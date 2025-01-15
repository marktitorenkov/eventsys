function selectDynamic(rootElement) {
  function debounce(f, interval) {
    let timer = null
    return (...args) => {
      clearTimeout(timer);
      return new Promise((resolve) => {
        timer = setTimeout(() => resolve(f(...args)), interval)
      })
    }
  }

  const fetchDebounced = debounce(fetch, 200)

  const [getSelected, createSelected, popSelected] = (function() {
    let selected = []

    function getSelected() {
      return selected.map(s => s.querySelector('input')).map(s => ({value: s.value, text: s.textContent}))
    }

    function createSelected(text, value) {
      if (!value || getSelected().map(s => s.value).includes(value)) {
        return null
      }

      const item = document.createElement('div')
      item.className = 'select-item'
      item.textContent = text

      const closeBtn = document.createElement('span');
      closeBtn.role = 'button'
      closeBtn.textContent = '✖'
      closeBtn.addEventListener('click', () => {
        selected = selected.filter(s => s != item)
        item.remove()
      })
      item.appendChild(closeBtn)

      const itemData = document.createElement('input')
      itemData.type = 'hidden'
      itemData.name = rootElement.name
      itemData.value = value
      item.appendChild(itemData)
  
      selected.push(item)
      return item
    }

    function popSelected() {
      if (!selected.length) return
      (selected.pop()).remove()
    }

    return [
      getSelected,
      createSelected,
      popSelected,
    ]
  })()

  const [getResults, setResults] = (function() {
    let results = null

    function setResults(resultsNew, onSelect) {
      results = resultsNew

      container.querySelector('.select-results')?.remove()
      if (!results) return
  
      const resultsContainer = document.createElement('div')
      resultsContainer.className = 'select-results'
      if (!results.length) {
        const noResults = document.createElement('div')
        noResults.className = 'no-result'
        noResults.textContent = 'No results'
        resultsContainer.appendChild(noResults)
      } else {
        for (let r of results) {
          const result = document.createElement('div')
          result.className = 'result'
          result.textContent = r.text
          result.dataset.value = r.value
          result.addEventListener('click', () => {
            onSelect(r.text, r.value)
          })
          resultsContainer.appendChild(result)
        }
      }
      container.appendChild(resultsContainer)
    }

    return [
      () => results,
      setResults,
    ]
  })()

  const container = document.createElement('div')
  container.className = 'select-dynamic'

  const inputContainer = document.createElement('div')
  inputContainer.className = 'select-input'
  const inputEl = document.createElement('input')
  inputEl.type = 'text'
  inputEl.size = 1

  function doSelect(text, value) {
    const item = createSelected(text, value)
    if (item) {
      inputContainer.insertAdjacentElement('beforebegin', item)
      inputEl.value = ''
      setResults(null)
    }
  }

  inputEl.addEventListener('input', () => {
    inputEl.setAttribute('size', inputEl.value.length + 1);

    setResults(null)
    if (inputEl.value) {
      fetchDebounced(rootElement.dataset.url+'?q='+encodeURIComponent(inputEl.value)+'&limit=5')
      .then(res => res.json())
      .then(res => {
        const selectedValues = getSelected().map(s => s.value)
        res = res.filter(r => !selectedValues.some(s => s == r.value))
        setResults(res, doSelect)
      })
    }
  })

  inputEl.addEventListener('keydown', (ev) => {
    if (ev.key === 'Enter' || ev.key === 'Tab') {
      const results = getResults()
      if (ev.key === 'Enter' || results) {
        ev.preventDefault()
      }
      if (results && results.length) {
        const text = results[0].text
        const value = results[0].value
        doSelect(text, value)
      }
    } else if (ev.key === 'Backspace') {
      if (!inputEl.value) {
        popSelected()
      }
    }
  })

  inputContainer.appendChild(inputEl)

  document.addEventListener('click', (evt) => {
    if (!container.contains(evt.target)) {
      setResults(null)
    }
  })

  container.appendChild(inputContainer)
  rootElement.parentElement.appendChild(container)
  rootElement.remove()
}

document.querySelectorAll('[data-select-dynamic]').forEach(selectDynamic)
