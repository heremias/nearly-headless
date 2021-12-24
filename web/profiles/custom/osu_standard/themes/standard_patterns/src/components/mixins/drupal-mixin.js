import axios from 'axios'
import dayjs from 'dayjs'
import validUrl from 'valid-url'

export default {
  props:{
      domain: {
          type: [String],
          validator:  function(value) {
              return value === undefined || validUrl.isWebUri(value)
          },
          default: () => {
            if(process.env.NODE_ENV === 'development') {
                return 'http://localhost:8888'
            }else{
                return ''
            }
          }
      },
      revisionPath: {
        type: String,
        default: '/api/practice/revisions/latest?_format=json'
      }
  },
  data: () => ({
    users: {},
    filters: {}
  }),
  methods: {
    async loadUsers(){
      //Load Users from Drupal
      const people = await axios.get(`${this.domain}/jsonapi/user/user`)
      const users = {}
      people.data.data.map( p => {
          users[p.id] = p.attributes.name
      })
      return users
    },
    async loadRevisions(){
      //Load revisions from Drupal
      let practices
      try{
        practices = await axios.get(`${this.domain}${this.revisionPath}`)
      } catch(err) {
        // eslint-disable-next-line no-console
        console.error(`Could not load revisions, is the path "${this.domain}${this.revisionPath}" setup on Drupal?`, err)
      }
      if(this.users === {}) await this.loadUsers()
      //Define each table row object
      const resultArray =  practices.data.map( (p, i) => ({
         i: i,
         title: p.title[0].value,
         user: this.users[p.revision_uid[0].target_uuid],
         path: p.path[0].alias,
         log: p.revision_log[0] ? p.revision_log[0].value : '',
         changed: dayjs(p.changed[0].value).fromNow(),
         isRecent: dayjs().diff(p.changed[0].value, 'days') < 30,
         updated: p.changed[0].value !== p.created[0].value,
         created: dayjs(p.created[0].value).fromNow(),
         site_tags: p.site_tags.map(t => t.target_uuid)
      }))
      return resultArray
    },
    async loadFilters(){
      const taxonomies = {
        "Priority Level": 'priority',
        "Media Type": 'media_type',
        "Impact": 'impact',
        "Role": 'roles'
      }
      for (var prop in taxonomies) {
        this.$set(this.filters, prop, [])
        this.$set(this.activeFilters, prop, [])
        let url = `${this.domain}/jsonapi/taxonomy_term/${taxonomies[prop]}`
        let xhr = await axios.get(url)
        let results = xhr.data.data
        results.map(result => {
          result.attributes.id = result.id
          result.attributes.taxonomy = prop
          result.attributes.type = taxonomies[prop]
          this.filters[prop].push(result.attributes)
        })

      }
    },
  }
}
