/**
 * 
 * @param {NodeListOf<HTMLFormElement>} existingForms
 * @param {HTMLFormElement} createForm 
 */
function pollsInit(existingForms, createForm) {
  /**
   * @param {HTMLFormElement} form
   * @param {HTMLElement?} submitter */
  async function getUpdatedForm(form, submitter) {
    const formData = new FormData(form);
    if (submitter) {
      formData.append(submitter.name, submitter.value)
    }
    const html = await (await fetch(form.action, {method: form.method, body: formData})).text()
    const t = document.createElement('template')
    t.innerHTML = html
    return t.content.children[0]
  }

  /**
   * @param {HTMLFormElement} form
   * @param {HTMLElement} submitter */
  async function updateForm(form, submitter) {
    const newForm = await getUpdatedForm(form, submitter)
    monitorForm(newForm)
    form.replaceWith(newForm)
  }

  /**
   * @param {HTMLFormElement} form */
  function monitorForm(form) {
    form.addEventListener('change', ev => {
      if (ev.target.type === 'checkbox') {
        updateForm(form)
      }
    })
    form.addEventListener('submit', ev => {
      ev.preventDefault()
      updateForm(form, ev.submitter)
    })
  }

  existingForms.forEach(monitorForm)

  createForm.addEventListener('submit', async ev => {
    ev.preventDefault()

    const newForm = await getUpdatedForm(createForm)
    createForm.reset()

    monitorForm(newForm)
    createForm.insertAdjacentElement('beforebegin', newForm)
  })
}
