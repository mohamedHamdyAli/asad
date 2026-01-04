import { ref } from 'vue'

const error = ref(null)
let timer = null

export function useServerError() {
  function show(err, duration = 4000) {
    clearTimeout(timer)

    const data = err?.response?.data || {}

    error.value = {
      title: data.key || 'Error',
      message: data.msg || data.message || err.message || 'Something went wrong',
    }

    timer = setTimeout(() => {
      error.value = null
    }, duration)
  }

  function clear() {
    error.value = null
    clearTimeout(timer)
  }

  return {
    error,
    show,
    clear,
  }
}
