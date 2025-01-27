/**
 * 
 * @param {HTMLFormElement} formEl
 * @param {HTMLElement} historyEl
 */
function chatInit(formEl, historyEl) {
  const CONTENT_KEY = 'content'
  const TIME_KEY = 'time_after'

  const contentEl = formEl.elements.namedItem(CONTENT_KEY)
  let last = null

  async function fetchMessages() {
    const formData = new FormData(formEl)
    formData.delete(CONTENT_KEY)
    formData.append(TIME_KEY, last?.message.time || null)
    const url = formEl.action+'?'+(new URLSearchParams(formData).toString())

    const lastBefore = last?.message;
    try {
      const res = await fetch(url)
      // If not equal another fetch has been processed
      // and the result of this fetch should be ignored.
      if (lastBefore !== last?.message) {
        return []
      }

      const messages =  await res.json()
      return messages
    } catch(e) {
      console.error(e)
      return []
    }
  }

  function newSeries(message) {
    const seriesEl = document.createElement('div')
    seriesEl.classList.add('message-series')
    if (message.viewer_is_owner) {
      seriesEl.classList.add('owner')
    } else {
      const nameEl = document.createElement('div')
      nameEl.className = 'sender-name'
      nameEl.textContent = message.sender_username
      seriesEl.appendChild(nameEl)
    }
    last = { seriesEl, message }
    return seriesEl
  }

  function processMessages(messages) {
    if (!messages || !messages.length) return

    const senderDifferent = (prev, curr) =>
      prev.sender_username != curr.sender_username
    const intervalAbove = (interval, prev, curr) => 
      (new Date(curr.time).getTime() - new Date(prev.time).getTime()) > interval

    const messagesFragment = document.createDocumentFragment()
    for (let message of messages) {
      if (!last || intervalAbove(10 * 60 * 1000, last.message, message)) {
        const dateEl = document.createElement('div')
        dateEl.className = 'messages-date'
        dateEl.textContent = new Date(message.time).toLocaleString('en-UK')
        messagesFragment.appendChild(dateEl)
        messagesFragment.appendChild(newSeries(message))
      }
      if (!last || senderDifferent(last.message, message)) {
        messagesFragment.appendChild(newSeries(message))
      }
      last.message = message

      const msgEl = document.createElement('div')
      msgEl.className = 'message'
      msgEl.textContent = message.content

      last.seriesEl.appendChild(msgEl)
    }

    historyEl.appendChild(messagesFragment)
    historyEl.scrollTo(0, historyEl.scrollHeight)
  }

  async function sendMessage() {
    if (! contentEl.value.trim()) {
      return
    }

    const formData = new FormData(formEl)
    contentEl.value = ''
    await fetch(formEl.action, {body: formData, method: 'post'})

    // After fetch immediately
    processMessages(await fetchMessages())
  }

  contentEl.addEventListener('keydown', ev => {
    if (ev.key === 'Enter' && !ev.shiftKey) {
      ev.preventDefault()
      sendMessage()
    }
  })

  formEl.addEventListener('submit', ev => {
    ev.preventDefault()
    sendMessage()
  })

  async function monitorMessages() {
    while (true) {
      processMessages(await fetchMessages())
      // Fallback if server side polling is disabled
      await new Promise(r => setTimeout(r, 1000));
    }
  }

  monitorMessages()
}
