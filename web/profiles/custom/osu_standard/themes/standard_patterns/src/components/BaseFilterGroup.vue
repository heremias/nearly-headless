<template>
  <b-form-group class="filter-group-container">
    <BaseAccordion
      :start-open="startOpen"
      v-model="isOpen"
      @toggle="onToggle"
      class="accordion"
    >
      <legend slot="header"> {{ label }} </legend>
      <BaseCheckbox
        v-model="showAll"
        :disabled="showAll"
        :label="defaultLabel"
        class="check"
      />
      <BaseCheckbox
        v-for="filter in shownOptions"
        v-model="computedFilters"
        :key="filter.id"
        :value="filter"
        :label="filter.name"
        class="check"
      />
      <a
        v-if="group.length > optionsPerGroup"
        @click.prevent="toggleReadMore"
        class="read-more"
        href="#"
      >Read {{isReadingMore ? 'Less' : 'More'}}
      </a>
    </BaseAccordion>
    <div
      v-if="!isOpen"
      class="accordion"
    >
      <BaseCheckbox
        v-if="showAll"
        v-model="showAll"
        :disabled="showAll"
        :label="defaultLabel"
      />
      <BaseCheckbox
        v-for="filter in value"
        v-model="computedFilters"
        :key="filter.id"
        :value="filter"
        :label="filter.name"
        class="check"
      />
    </div>
  </b-form-group>
</template>

<script>
import {BFormGroup} from 'bootstrap-vue/esm/components/form-group'
import BaseAccordion from '@umarketing/osu-design-system/src/molecules/BaseAccordion.vue'
import BaseCheckbox from '@umarketing/osu-design-system/src/molecules/BaseCheckbox.vue'


export default {
  components: {
    BaseAccordion,
    BaseCheckbox,
    'b-form-group': BFormGroup,
  },
  props: {
    defaultLabel: {
      default: 'All',
      type: String
    },
    startOpen: {
      type: Boolean,
      default() {
        return true
      }
    },
    value: {
      type: Array,
      default: () => []
    },
    label: {
      type: String,
      default: ''
    },
    group: {
      type: Array,
      default: () => []
    },
    optionsPerGroup : {
      type: Number,
      default: 5
    }
  },
  data() {
    return {
      isReadingMore: false,
      isOpen: true
    }
  },
  computed: {
    shownOptions() {
      return this.isReadingMore ? this.group : this.group.slice(0, this.optionsPerGroup)
    },
    selected() {
      return this.group
    },
    computedFilters: {
      get() {
        return this.value
      },
      set(filters) {
        this.$emit('input', filters)
      }
    },
    showAll: {
      get() {
        return this.value.length === 0
      },
      set(val) {
        if (val !== false) {
          const payload = {}
          payload[this.label] = []
          this.$emit('input', [])
        }
      }
    }
  },
  created: function() {
    this.isOpen = this.startOpen
  },
  methods: {
    toggleReadMore() {
      this.isReadingMore = !this.isReadingMore
    },
    onToggle(state) {
      this.isOpen = state
    },
    handleInput(input, filter) {
      if (input === false) {
        this.$emit('filter:remove', filter)
      } else {
        this.$emit('filter:add', filter)
      }
    }
  }
}
</script>

<style lang="css">
.check label {
  line-height: 1.2rem !important;
}
.check > div {
  padding-right: 0 !important;
}
.accordion > div > div {
  padding-left: 30px;
}
</style>

<style lang="scss" scoped>
.read-more {
  font-family: $font-heading;
}
  .check {
    @include stack-space($space-xs);
  }

.filter-group-container {
  font-family: $font-heading;
  color: rgb(33, 37, 41);
}

</style>

<style scoped>

.filter-group-container {
  padding: 0;
}
.filter-group-container >>> .accordion .header {
  padding-bottom:5px;
  padding-top:5px;
  height: auto;
  border-bottom: solid 2px #ccc;
}
.filter-group-container >>> label {
  font-size:16px;
  line-height: 24px;
}
.filter-group-container >>> legend {
  font-weight: 550;
  font-size: 1.125em !important;
  line-height: 1.125em !important;

}
</style>
