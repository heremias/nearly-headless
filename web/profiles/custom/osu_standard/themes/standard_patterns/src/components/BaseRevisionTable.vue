<template>
  <BaseTable
    class="revision-table"
    :search="false"
    :pagination="pagination"
    :bordered="false"
    :rows="rows"
    :is-loading="isLoading"
    v-on="$listeners"
    v-bind="$props"
  >
    <template slot="header">
      <th class="main-column">Activity</th>
      <th></th>
    </template>
    <template v-slot:row="{row}">
      <td>
        <div class="practice-title">
          <span
            class="badge"
            v-if="row.isRecent"
          >
            <Notification
              class="icon"
            />
            <span> Recent Update</span>
          </span>
          <a :href="row.path">{{ row.title }}</a>
        </div>
        {{ row.updated ? 'Updated' : 'Created'}} best practice:
        {{ row.log }}
      </td>
      <td class="text-right">  {{ row.changed }}  by {{ row.user }}</td>
    </template>
  </BaseTable>
</template>

<script>
import BaseTable from '@umarketing/osu-design-system/src/organisms/BaseTable.vue'
import Notification from '../assets/icons/notification-bell.svg'

export default {
  name: 'BaseRevisionTable',
  components: { BaseTable, Notification },
  props:{
      pagination: {
          type: Boolean,
          default: false
      },
      rows: {
          type: Array,
          default: () => []
      },
      limit: {
        type: Number,
        default: undefined
      },
      lastPage: {
        type: Number,
        default: Infinity
      },
      page: {
        type: Number,
        default: undefined
      },
      total: {
        type: Number,
        default: undefined
      },
      isLoading: {
        type: Boolean,
        default: false
      }
  }
}
</script>

<style scoped lang="scss">
  .main-column {
    width: 75%;
  }
  .practice-title {
    margin-bottom: 7px;
  }
  .badge {
    margin-right: 7px;
    padding-top:6px;
    padding-right:6px;
    background: #FFEAAE;
  }
  .icon {
    padding:0;
    margin-left:3px;
    margin-right:3px;
    height: 1.5em;

    .cls-1, .cls-2{
      fill: #DCAA38;
    }
  }
</style>
