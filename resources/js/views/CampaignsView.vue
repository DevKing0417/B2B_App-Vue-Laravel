<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Campaigns</h1>
        <router-link
          to="/campaigns/create"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Create Campaign
        </router-link>
      </div>

      <!-- Filters -->
      <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select
              id="status"
              v-model="filters.status"
              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
              <option value="">All</option>
              <option value="active">Active</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div>
            <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
            <select
              id="sort"
              v-model="filters.sort"
              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
              <option value="newest">Newest First</option>
              <option value="oldest">Oldest First</option>
              <option value="amount">Amount Raised</option>
            </select>
          </div>
          <div>
            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
            <input
              type="text"
              id="search"
              v-model="filters.search"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="Search campaigns..."
            />
          </div>
        </div>
      </div>

      <!-- Campaigns Grid -->
      <div v-if="isLoading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>
      
      <div v-else-if="filteredCampaigns.length === 0" class="text-center py-12">
        <h3 class="text-lg font-medium text-gray-900">No campaigns found</h3>
        <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
      </div>

      <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <CampaignCard
          v-for="campaign in filteredCampaigns"
          :key="campaign.id"
          :campaign="campaign"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import CampaignCard from '@/components/CampaignCard.vue';
import { useCampaignStore } from '@/stores/campaign';

const campaignStore = useCampaignStore();
const isLoading = ref(true);

const filters = ref({
  status: '',
  sort: 'newest',
  search: '',
});

const filteredCampaigns = computed(() => {
  let campaigns = [...campaignStore.campaigns];

  // Apply status filter
  if (filters.value.status) {
    campaigns = campaigns.filter(campaign => campaign.status === filters.value.status);
  }

  // Apply search filter
  if (filters.value.search) {
    const searchTerm = filters.value.search.toLowerCase();
    campaigns = campaigns.filter(campaign => 
      campaign.title.toLowerCase().includes(searchTerm) ||
      campaign.description.toLowerCase().includes(searchTerm)
    );
  }

  // Apply sorting
  switch (filters.value.sort) {
    case 'oldest':
      campaigns.sort((a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime());
      break;
    case 'amount':
      campaigns.sort((a, b) => b.current_amount - a.current_amount);
      break;
    default: // newest
      campaigns.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
  }

  return campaigns;
});

onMounted(async () => {
  try {
    await campaignStore.fetchCampaigns();
  } catch (error) {
    console.error('Failed to fetch campaigns:', error);
  } finally {
    isLoading.value = false;
  }
});
</script> 