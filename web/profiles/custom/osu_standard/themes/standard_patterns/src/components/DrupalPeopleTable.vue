<template>
  <div :class="$style.container">
    <DrupalPersonCard
      v-for="person in people"
      :key="person.title"
      :person="person"
      :site-tags="siteTags"
    />
  </div>
</template>

<script>
import DrupalPersonCard from './DrupalPersonCard.vue'
  export default {
    components: { DrupalPersonCard },
    props: {
      isLoading: {
        type: Boolean,
        default: false
      },
      siteTags: Object,
      people: {
        type: Array,
        default: () => []
      }
    }
  }
</script>

<style lang="scss" module>
  .container {
    @include inset-space($space-m);
  }
</style>
