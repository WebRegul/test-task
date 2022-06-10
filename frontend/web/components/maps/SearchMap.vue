<template>
  <client-only>
    <yandex-map
      :coords="center"
      :zoom="zoom"
      :use-object-manager="true" :object-manager-clusterize="true"
      :use-html-in-layout="true"
      :options="mapOptions"
      :controls="['zoomControl', 'typeSelector']"
      @update:bounds="$emit('update:bounds', $event)"
      style="width: 100%; height: 100%"
    >
      <ymap-marker v-for="item in points" :key="item.id"
        :marker-id="item.id"
        :coords="item.coords.coordinates"
        :options="{ preset: 'islands#blackStretchyIcon' }"
        :properties="{ iconContent: '5 000 ₽' }"
         @balloonopen="onBalloonOpenHandler(item.id)"
                   :callbacks="{ balloonopen: onBalloonOpenHandler(item.id) }"
      >
        <object-card slot="balloon" orientation="vertical" map></object-card>
      </ymap-marker>
    </yandex-map>
  </client-only>
</template>

<script>
import { yandexMap, ymapMarker } from 'vue-yandex-maps'
import { mapGetters, mapState, mapActions } from "vuex";
import ObjectCard from '../../components/cards/ObjectCard'

export default {
  name: 'SearchMap',
  components: { ObjectCard, yandexMap, ymapMarker },
  data() {
    return {
      mapOptions: {
        maxZoom: 24,
        minZoom: 6,
        suppressMapOpenBlock: true,

      },
      marker: {
        icon: {
          preset: 'islands#blackStretchyIcon',
          layout: 'default#imageWithContent', // 'default#imageWithContent' для использования с контентом
          // imageHref: markerIcon, // адрес изображения или data:image/svg+xml;base64
          imageSize: [100, 20], // размер иконки в px
          // imageOffset: [-22, -55], // смещение иконки в px,
          /* Следующие поля актуальны для layout: 'default#imageWithContent' */
          content: '5 000', // содержимое контента
          // contentOffset: [-22, -55], // смещение контента в px,
          contentLayout:
            '<div style="color: #000; font-weight: bold; width: 100px;">$[properties.iconContent]</div>', // строковый HTML шаблон для контента
        },
      },
    }
  },
  computed:{
    ...mapGetters({
      center: 'web/geo/cityCoords',
    }),
    ...mapState({
      city: state => state.web.geo.city,
      points: state => state.web.search.points
    }),
    zoom(){
      return this.city?.zoom || 10;
    }
  },
  methods:{
    ...mapActions({

    }),
    onBalloonOpenHandler(e){
      console.log(e)
    }
  }
}
</script>

<style scoped></style>
