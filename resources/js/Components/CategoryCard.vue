<script setup>
import { Link } from "@inertiajs/vue3";
import Skeleton from "primevue/skeleton";

defineProps({
  category: {
    type: [Array, Object],
    default: () => {},
  },
  skeleton: {
    type: Boolean,
    default: () => false,
  },
});
</script>

<template>
  <div v-if="skeleton" class="category-card skeleton relative">
    <Skeleton height="200px" width="100%" />
    <div class="m-4">
      <p class="mb-2">
        <Skeleton width="100%" height="2rem" />
      </p>
      <p>
        <Skeleton width="100%" height="1rem" />
      </p>
    </div>
  </div>
  <div
    v-else
    class="category-card transition ease-in-out hover:scale-110 relative"
  >
    <img
      :src="
        category.hero_preview
          ? category.hero_preview
          : '/assets/no-category-img.png'
      "
      :alt="category.name"
      class="w-full h-32 sm:h-48 object-cover"
    />
    <div class="my-4">
      <span class="font-bold">{{ category.name }}</span>
    </div>

    <Link
      v-if="!skeleton"
      :href="route('category.show', { category: category.slug })"
      class="absolute inset-0"
    />
  </div>
</template>
<style lang="scss">
.badge {
  --tw-bg-opacity: 1;
  background-color: rgb(var(--surface-900));
  border-radius: 9999px;
  font-weight: 700;
  font-size: 0.75rem;
  line-height: 1rem;
  margin-top: 0.5rem;
  margin-left: 0.5rem;
  padding: 0.5rem;
  position: absolute;
  top: 0px;
  color: rgb(var(--primary-600));
  text-transform: uppercase;
}
</style>
