<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create New Campaign</h1>
        <p class="mt-2 text-sm text-gray-600">
          Fill in the details below to create a new donation campaign.
        </p>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Campaign Image -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Campaign Image</label>
          <div class="mt-1 flex items-center">
            <div
              v-if="!form.image"
              class="flex-1 flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
            >
              <div class="space-y-1 text-center">
                <PhotographIcon class="mx-auto h-12 w-12 text-gray-400" />
                <div class="flex text-sm text-gray-600">
                  <label
                    for="image-upload"
                    class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                  >
                    <span>Upload an image</span>
                    <input
                      id="image-upload"
                      type="file"
                      accept="image/*"
                      class="sr-only"
                      @change="handleImageUpload"
                    />
                  </label>
                  <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
              </div>
            </div>
            <div v-else class="flex-1">
              <img
                :src="form.imagePreview"
                class="h-32 w-full object-cover rounded-md"
                alt="Campaign preview"
              />
              <button
                type="button"
                @click="removeImage"
                class="mt-2 text-sm text-red-600 hover:text-red-500"
              >
                Remove image
              </button>
            </div>
          </div>
        </div>

        <!-- Title -->
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
          <input
            type="text"
            id="title"
            v-model="form.title"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            placeholder="Enter campaign title"
            required
          />
        </div>

        <!-- Description -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            id="description"
            v-model="form.description"
            rows="4"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            placeholder="Enter campaign description"
            required
          ></textarea>
        </div>

        <!-- Target Amount -->
        <div>
          <label for="target_amount" class="block text-sm font-medium text-gray-700">Target Amount</label>
          <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="text-gray-500 sm:text-sm">$</span>
            </div>
            <input
              type="number"
              id="target_amount"
              v-model="form.target_amount"
              min="0"
              step="0.01"
              class="block w-full pl-7 pr-12 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="0.00"
              required
            />
          </div>
        </div>

        <!-- End Date -->
        <div>
          <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
          <input
            type="date"
            id="end_date"
            v-model="form.end_date"
            :min="minDate"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            required
          />
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
          <router-link
            to="/campaigns"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </router-link>
          <button
            type="submit"
            :disabled="isLoading"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <span v-if="isLoading" class="mr-2">
              <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ isLoading ? 'Creating...' : 'Create Campaign' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCampaignStore } from '@/stores/campaign';
import { PhotographIcon } from '@heroicons/vue/24/outline';

const router = useRouter();
const campaignStore = useCampaignStore();
const isLoading = ref(false);

const form = ref({
  title: '',
  description: '',
  target_amount: '',
  end_date: '',
  image: null as File | null,
  imagePreview: '',
});

const minDate = computed(() => {
  const today = new Date();
  return today.toISOString().split('T')[0];
});

const handleImageUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    const file = input.files[0];
    if (file.size > 10 * 1024 * 1024) {
      alert('Image size should be less than 10MB');
      return;
    }
    form.value.image = file;
    form.value.imagePreview = URL.createObjectURL(file);
  }
};

const removeImage = () => {
  form.value.image = null;
  form.value.imagePreview = '';
};

const handleSubmit = async () => {
  try {
    isLoading.value = true;
    const formData = new FormData();
    formData.append('title', form.value.title);
    formData.append('description', form.value.description);
    formData.append('target_amount', form.value.target_amount);
    formData.append('end_date', form.value.end_date);
    if (form.value.image) {
      formData.append('image', form.value.image);
    }

    await campaignStore.createCampaign(formData);
    router.push('/campaigns');
  } catch (error) {
    console.error('Failed to create campaign:', error);
  } finally {
    isLoading.value = false;
  }
};
</script> 