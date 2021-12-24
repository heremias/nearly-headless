<template>
  <div class="filter-container">
    <div class="title-row">
      <strong class="title">Filter By</strong>
      <a
        class="clear"
        @click.prevent="clearFilters"
        href="#"
      >Reset filters</a>
    </div>

    <b-form>
      <b-input-group
        v-for="(group, label) in filters"
        :key="label"
      >
        <BaseFilterGroup
          :filters="filters"
          :base-filters="baseFilters"
          :label="label"
          :group="group"
          :start-open="startOpen"
          :value="value[label]"
          :default-label="defaultLabel"
          @input="updateFilters($event, label)"
        />
      </b-input-group>
    </b-form>
  </div>
</template>

<script>
//:active-filters="activeFilters[label]"
import {BForm} from 'bootstrap-vue/esm/components/form'
import {BInputGroup} from 'bootstrap-vue/esm/components/input-group'
import BaseFilterGroup from './BaseFilterGroup.vue'

export default {
  components: {
    'b-form': BForm,
    'b-input-group': BInputGroup,
    BaseFilterGroup
  },
  props: {
    startOpen: {
      default: true,
      type: Boolean
    },
    defaultLabel: {
      default: 'All',
      type: String
    },
    value: {
      type: Object,
      default: () => {}
    },
    filters: {
      type: Object,
      default: () => {}
    },
    baseFilters: {
      type: Array,
      default: () => []
    }
  },
  created: function() {
    const initialValue = {}
    const labels = Object.keys(this.filters)

    labels.map(
      l => (initialValue[l] = Array.isArray(this.value[l]) ? this.value[l] : [])
    )
    this.$emit('input', initialValue)
  },
  methods: {
    updateFilters(filters, label) {
      const filterGroup = {}
      filterGroup[label] = filters
      const payload = Object.assign({}, this.value, filterGroup)
      this.$emit('input', payload)
    },
    clearFilters() {
      const empty = {}
      const labels = Object.keys(this.filters)
      labels.map(l => (empty[l] = []))

      this.$emit('input', empty)
    }
  }
}
</script>

<style scoped>
  .title-row {
    margin-bottom: 10px;
  }
  .title {
    text-transform: uppercase;
  }
.filter-group-container {
  min-width: 100%;
  display:block;

}

.filter-container {
  background: #F7F7F7;
  padding: 15px 15px;
  color: #212529;
}
.clear {
  font-size: .75em;
  float: right;
}
</style>
