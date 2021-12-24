import { transform } from 'node-json-transform'
import jsonApi from './jsonApiClient.js'

const transformMap = {
  item: {
    id: 'id',
    image: 'image.data.image.data.uri.url',
    title: 'title',
    slug: 'slug',
    site_tags: 'site_tags.data',
    body: 'body',
    path: 'path',
    preferred_title: 'preferred_title',
    preferred_organization: 'preferred_organization',
    preferred_email: 'preferred_email',
    liason_email: 'liason_email',
    liason_name: 'liason_name',
    liason_phone: 'liason_phone',
    preferred_phone: 'preferred_phone',
    drupal_internal__nid: 'drupal_internal__nid',
    weight: 'weight',
  },
  each: (person) => {
    person.thumbnail = getThumbnail(person.image)
    return person
  }
}

function getThumbnail(url) {
  if(!url) return '/profiles/custom/osu_standard/themes/standard_patterns/images/buckeye-leaf.svg'
  return url.replace("/sites/default/files/", "/sites/default/files/styles/square_small/public/")
}

export default {
  async get (options) {
    const data = await jsonApi.get('/node/person', options)
    const transformed = transform(data.data, transformMap)
    return transformed
  }
}
