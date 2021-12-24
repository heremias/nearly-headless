<template>
  <article class="n-teaser-person" >
  <div class="n-teaser-person__inner ">
    <div class="n-teaser-person__image">
      <img :src="person.thumbnail" :alt="person.title" style="width: 100%;">
    </div>
    <div class="n-teaser-person__summary">
      <div class="n-teaser-person__name">
        <a :href="person.path ? person.path.alias : ''">{{person.title}}</a>
      </div>
      <div class="n-teaser-person__job-title">
        <div class="field field--name-preferred-title field--type-string field__item">
          {{ person.preferred_title }}
        </div>
        <div class="field field--name-preferred-organization field--type-string field__item">
          {{ person.preferred_organization }}
        </div>
      </div>
      <div class="n-teaser-person__email">
        {{ person.preferred_email }}
      </div>
      <div class="n-teaser-person__phone">
        {{ person.preferred_phone }}
      </div>
          <div
            v-for="(vocab, taxonomy) in vocabs"
            :key="taxonomy"
            :class="`n-teaser-person__tags n-teaser-person__tags-${ taxonomy }`"
          >
            <span>{{ taxonomy }}:</span>
              {{vocab}}
          </div>
    </div>
    <div class="n-teaser-person__liason" style="padding:20px;"
      v-if="person.liason_name || person.liason_phone || person.liason_email"
    >
      <div>
        <b>Media Liason:</b>
        <div v-if="person.liason_name">
          {{person.liason_name}}
        </div>
        <div v-if="person.liason_email">
          <a :href="`mailto:${person.liason_email}`">{{person.liason_email}}</a>
        </div>
        <div v-if="person.liason_phone">
          <a :href="`tel:${person.liason_phone}`">{{person.liason_phone}}</a>
        </div>
      </div>
    </div>
  </div>
</article>

</template>

<script>
  export default {
    props: {
      person: {
        type: Object
      },
      siteTags: {
        type: Object
      }
    },
    computed: {
      assignedTags() {
        return this.person.site_tags.filter(t => t.changed )
      },
      vocabs() {
        const vocabs = {}
        const types = Object.keys(this.siteTags)
        types.forEach(type => {
          const title = this.siteTags[type].title
          const tags = this.assignedTags.filter(t =>{
            return t.type.includes(type)
          })
          if(tags.length) {
            vocabs[title] = tags.map(t => t.name).join('; ')
          }
        })
        return vocabs
      },
      filteredVocab() {
        // we only want the site_tags with assigned terms
        return this.person.site_tags.filter(t => t.terms?.length > 0)
      }
    }
  }
</script>

<style lang="scss" >
  /* Relying on legacy theme stylesheet for styling. */
  .n-teaser-person__summary { flex: 2; }
  .n-teaser-person {
    @include stack-space($space-m);
  }
</style>
