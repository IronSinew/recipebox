<script setup>
import { Link } from "@inertiajs/vue3";
import Skeleton from "primevue/skeleton";

defineProps({
  recipe: {
    type: [Array, Object],
    default: () => {},
  },
  skeleton: {
    type: Boolean,
    default: () => false,
  },
});

const humanReadableDuration = (durationInMinutes) => {
  const hours = Math.floor(durationInMinutes / 60);
  const minutes = Math.floor(durationInMinutes % 60);
  const phrases = [];
  phrases.push(hours > 0 ? `${hours}H` : null);
  phrases.push(minutes > 0 ? `${minutes}M` : null);

  return phrases.filter((e) => e).join(" ");
};
</script>

<template>
  <div v-if="skeleton" class="recipe-card skeleton relative">
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
    class="recipe-card transition ease-in-out hover:scale-110 relative"
  >
    <img
      :src="
        recipe.hero_preview ? recipe.hero_preview : '/assets/no-recipe-img.png'
      "
      :alt="recipe.name"
      class="w-full h-32 sm:h-48 object-cover"
    />
    <div class="my-4 text-main-dark dark:text-white">
      <span class="font-bold">{{ recipe.name }}</span>
      <div class="flex items-center gap-10">
        <div class="flex items-center">
          <svg
            class="w-5 inline-block"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <span class="ml-1">{{
            humanReadableDuration(recipe.total_time)
          }}</span>
        </div>
        <div class="flex items-center">
          <svg
            class="w-5 inline-block stroke-black dark:stroke-white"
            viewBox="0 0 24 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M2.5 13.5C2.5 8.25 6.75 4 12 4C17.24 4 21.5 8.25 21.5 13.5"
              stroke-width="1.3"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <path
              d="M2.5 13.5C2.5 8.25 6.75 4 12 4C17.24 4 21.5 8.25 21.5 13.5"
              stroke-width="1.3"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <path
              d="M10.2 6.87805C7.88002 7.50805 6.06002 9.29805 5.40002 11.6081"
              stroke-width="1.3"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <path
              d="M1 15.5H23"
              stroke-width="1.3"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <path
              d="M10.417 2.79201C10.407 1.91201 11.117 1.20201 11.997 1.20201C12.867 1.19201 13.577 1.90201 13.577 2.78201"
              stroke-width="1.3"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <path
              d="M2.5 15.5C3.23 16.32 3.887 18.5 6.22 18.5H17.77C20.1 18.5 20.76 16.32 21.49 15.5"
              stroke-width="1.3"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>

          <span class="ml-1">
            {{ recipe.serving }}
          </span>
        </div>
      </div>
    </div>
    <!-- <div class="badge">
      <svg
        class="w-5 inline-block"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
      <span class="ml-1">{{ humanReadableDuration(recipe.total_time) }}</span>
    </div> -->
    <Link
      v-if="!skeleton"
      :href="route('recipe.show', { recipe: recipe.slug })"
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
