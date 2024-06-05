<script setup>
import { router } from "@inertiajs/vue3";
import Button from "primevue/button";

// import Card from "primevue/card";
import CategoryCard from "@/Components/CategoryCard.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({
  categories: {
    type: [Array, Object],
    required: false,
    default() {
      return [];
    },
  },
});
</script>

<template>
  <AppLayout title="Categories">
    <div
      class="angled-recipe-header relative bg-[url('/assets/pattern.jpg')] py-20 lg:py-40 bg-center"
    >
      <div class="container mx-auto px-10 lg:px-0">
        <img
          :src="'/assets/category-hero-img.svg'"
          :alt="'Explore Our Flavourful Categories'"
          class="w-full object-cover"
        />
      </div>
    </div>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
          <CategoryCard
            v-for="category in categories"
            :key="category.id"
            :category="category"
            style="overflow: hidden; position: relative"
          >
            <template #link>
              <Button
                :label="`View all ${category.recipes_count} recipes`"
                icon="pi pi-arrow-right"
                icon-pos="right"
                class="!text-white !text-xs !py-1"
                @click="
                  router.visit(
                    route('category.show', {
                      category: category.slug,
                    }),
                  )
                "
              />
            </template>
          </CategoryCard>

          <!-- <Card>
             <template #title>
              {{ category.name }}
            </template>
            <template #content>
              <Link
                :href="
                  route('category.show', {
                    category: category.slug,
                  })
                "
                style="position: absolute; top: 0; left: 0; bottom: 0; right: 0"
              />
              <Button
                :label="`View all ${category.recipes_count} recipes`"
                icon="pi pi-arrow-right"
                icon-pos="right"
                class="float-right !text-white"
                @click="
                  router.visit(
                    route('category.show', {
                      category: category.slug,
                    }),
                  )
                "
              />
              <div class="clear-both" />
            </template> 
          </Card>-->
        </div>
      </div>
    </div>
  </AppLayout>
</template>
