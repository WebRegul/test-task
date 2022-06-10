import {mapActions} from "vuex";

export default {
  layout(ctx) {
    return ctx.$device.isDesktop ? 'objectsConstructor' : 'objectsConstructorMobile';
  },
  methods: {
    ...mapActions({
      getParams: 'cabinet/objects/getParams',
      getObjectItem: 'cabinet/objects/getObjectItem'
    }),
  },
  mounted() {
    this.getParams();
  }
}
