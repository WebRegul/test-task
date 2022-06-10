export default {
  data() {
    return {
      error: false,
      errorMessage: null,
      disabled: false,
      loading: false
    }
  },
  methods: {
    resetError() {
      this.error = false;
      this.errorMessage = ''
    },
  }
}
