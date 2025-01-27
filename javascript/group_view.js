(function() {
  const KEYS = ['chat', 'polls']
  const switchPanel = tabs_init({keys: KEYS, onClick: (key) => (location.hash = key, true) })
  const cleanHash = (hash) => hash.replace('#', '')
  window.addEventListener('hashchange', () => switchPanel(cleanHash(location.hash)))
  switchPanel(cleanHash(location.hash) || KEYS[0])

  const chatForm = document.getElementById('chat-form')
  const chatHistory = document.getElementById('chat-history')
  if (chatForm && chatHistory) {
    chatInit(chatForm, chatHistory)
  }

  const pollsForms = document.querySelectorAll('form.poll-form:not(.create)')
  const pollCreateForm = document.querySelector('form.poll-form.create')
  if (pollsForms && pollCreateForm) {
    pollsInit(pollsForms, pollCreateForm);
  }
})()
