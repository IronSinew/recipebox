import { router } from '@inertiajs/vue3'

export function makeHero (id, onSuccess, onFinish, onError) {
  // eslint-disable-next-line
  router.post(route('admin.images.make_hero', { media: id }), {}, {
    onFinish: () => {
      if (typeof onFinish !== 'undefined') {
        onFinish()
      }
    },
    onSuccess: () => {
      if (typeof onFinish !== 'undefined') {
        onSuccess()
      }
    },
    onError: () => {
      if (typeof onFinish !== 'undefined') {
        onError()
      }
    }
  })
}
export function destroyHero (id, onSuccess, onFinish, onError) {
  // eslint-disable-next-line
  router.delete(route('admin.images.destroy', { media: id }), {
    onFinish: () => {
      if (typeof onFinish !== 'undefined') {
        onFinish()
      }
    },
    onSuccess: () => {
      if (typeof onFinish !== 'undefined') {
        onSuccess()
      }
    },
    onError: () => {
      if (typeof onFinish !== 'undefined') {
        onError()
      }
    }
  })
}
