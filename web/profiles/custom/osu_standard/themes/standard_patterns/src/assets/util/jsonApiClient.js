/**
 * Client for JSON API server.
 * We use "jsonapi-parse" package to format responses :
 * It resolves relationships and included objects nicely in the final data object.
 */

import Kitsu from 'kitsu'
import * as rax from 'retry-axios'
import * as queryString from 'query-string'

const config = {
  baseURL: '/jsonapi',
  pluralize: false,
  withCredentials: true
}

// Retry automatically 5 times to prevent random build errors
const interceptorId = rax.attach()

// using Kitsu to automatically flatten the response from JSONAPI and
// attach related Drupal entities (images, terms)
const api = new Kitsu(config)

export default {
  get: async (uri, params = null) => {
    const response = await getRequest(uri, params)
    // const recursed = await recurseRequest(response, uri, params)
    return response
  }
}

async function getRequest (uri, params) {
  try {
    const response = await api.get(uri, params)
    return response
  } catch (err) {
    const url = config.baseURL + '/' + uri
    console.error('Error while getting data', url, err)
  }
}

/**
 *  Recursivley builds a full list of the requested data by making additional
 *  requests as long as their is a "next" record from Drupal.
 * @param {Object} response JSONAPI response object
 * @param {Object} params The request params
 */
async function recurseRequest (response, uri, params) {
  if (!response.links.next) {
    return response.data
  }
  const query = `?${response.links.next.href.split('?')[1]}`
  const parsed = queryString.parse(query)
  const newResponse = await getRequest(uri, parsed)
  newResponse.data = [...response.data, ...newResponse.data]
  return recurseRequest(newResponse, uri, params)
}
