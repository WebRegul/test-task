

export default function ({ store, redirect }) {
  console.log(process.server);
  if (!process.server) {
    // store.dispatch('web/geo/cities');
  }

}
