<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900">Campaign Management</h1>
      <div class="flex space-x-3">
        <button
          @click="refreshCampaigns"
          class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <ArrowPathIcon class="h-4 w-4 mr-2" />
          Refresh
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div>
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status"
            v-model="filters.status"
            class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>

        <div>
          <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
          <select
            id="category"
            v-model="filters.category"
            class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category" :value="category">
              {{ category }}
            </option>
          </select>
        </div>

        <div>
          <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
          <input
            type="text"
            id="search"
            v-model="filters.search"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            placeholder="Search campaigns..."
          />
        </div>

        <div>
          <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
          <select
            id="sort"
            v-model="filters.sort"
            class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
          >
            <option value="created_at:desc">Newest First</option>
            <option value="created_at:asc">Oldest First</option>
            <option value="current_amount:desc">Most Funded</option>
            <option value="current_amount:asc">Least Funded</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Campaigns List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div v-if="isLoading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>

      <div v-else-if="error" class="p-4">
        <div class="bg-red-50 border-l-4 border-red-400 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <ExclamationCircleIcon class="h-5 w-5 text-red-400" />
            </div>
            <div class="ml-3">
              <p class="text-sm text-red-700">{{ error }}</p>
            </div>
          </div>
        </div>
      </div>

      <div v-else>
        <ul class="divide-y divide-gray-200">
          <li v-for="campaign in filteredCampaigns" :key="campaign.id" class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex-1 min-w-0">
                <div class="flex items-center">
                  <h2 class="text-lg font-medium text-gray-900 truncate">{{ campaign.title }}</h2>
                  <span
                    :class="[
                      'ml-2 px-2 py-1 text-xs font-semibold rounded-full',
                      getStatusClass(campaign.status)
                    ]"
                  >
                    {{ campaign.status }}
                  </span>
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                  <span>Created by {{ campaign.creator.name }}</span>
                  <span class="mx-2">•</span>
                  <span>Target: ${{ campaign.target_amount.toLocaleString() }}</span>
                  <span class="mx-2">•</span>
                  <span>Raised: ${{ campaign.current_amount.toLocaleString() }}</span>
                </div>
              </div>
              <div class="ml-4 flex-shrink-0 flex space-x-2">
                <button
                  v-if="campaign.status === 'pending'"
                  @click="approveCampaign(campaign.id)"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  Approve
                </button>
                <button
                  v-if="campaign.status === 'active'"
                  @click="cancelCampaign(campaign.id)"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  Cancel
                </button>
                <button
                  @click="editCampaign(campaign.id)"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Edit
                </button>
              </div>
            </div>
          </li>
        </ul>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="currentPage--"
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Previous
            </button>
            <button
              @click="currentPage++"
              :disabled="currentPage === totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Next
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing
                <span class="font-medium">{{ (currentPage - 1) * itemsPerPage + 1 }}</span>
                to
                <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, totalItems) }}</span>
                of
                <span class="font-medium">{{ totalItems }}</span>
                results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                  v-for="page in totalPages"
                  :key="page"
                  @click="currentPage = page"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    page === currentPage
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                  ]"
                >
                  {{ page }}
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCampaignStore } from '@/stores/campaign';
import {
  ArrowPathIcon,
  ExclamationCircleIcon,
} from '@heroicons/vue/24/outline';

const router = useRouter();
const campaignStore = useCampaignStore();

const isLoading = ref(false);
const error = ref<string | null>(null);
const campaigns = ref<any[]>([]);
const categories = ref<string[]>([]);

const filters = ref({
  status: '',
  category: '',
  search: '',
  sort: 'created_at:desc',
});

const currentPage = ref(1);
const itemsPerPage = 10;
const totalItems = ref(0);

const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage));

const filteredCampaigns = computed(() => {
  let result = [...campaigns.value];

  // Apply filters
  if (filters.value.status) {
    result = result.filter(campaign => campaign.status === filters.value.status);
  }
  if (filters.value.category) {
    result = result.filter(campaign => campaign.category === filters.value.category);
  }
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase();
    result = result.filter(campaign =>
      campaign.title.toLowerCase().includes(search) ||
      campaign.description.toLowerCase().includes(search)
    );
  }

  // Apply sorting
  const [sortField, sortOrder] = filters.value.sort.split(':');
  result.sort((a, b) => {
    if (sortOrder === 'asc') {
      return a[sortField] > b[sortField] ? 1 : -1;
    } else {
      return a[sortField] < b[sortField] ? 1 : -1;
    }
  });

  // Apply pagination
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return result.slice(start, end);
});

type CampaignStatus = 'pending' | 'active' | 'completed' | 'cancelled';

const getStatusClass = (status: string) => {
  const classes: Record<CampaignStatus, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    active: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return classes[status as CampaignStatus] || 'bg-gray-100 text-gray-800';
};

const refreshCampaigns = async () => {
  try {
    isLoading.value = true;
    error.value = null;
    // Fetch campaigns from API
    // const response = await axios.get('/api/admin/campaigns', {
    //   params: {
    //     page: currentPage.value,
    //     per_page: itemsPerPage,
    //     ...filters.value
    //   }
    // });
    // campaigns.value = response.data.data;
    // totalItems.value = response.data.total;
  } catch (err: unknown) {
    console.error('Failed to fetch campaigns:', err);
    error.value = 'Failed to load campaigns';
  } finally {
    isLoading.value = false;
  }
};

const approveCampaign = async (id: number) => {
  try {
    // await axios.post(`/api/admin/campaigns/${id}/approve`);
    await refreshCampaigns();
  } catch (error) {
    console.error('Failed to approve campaign:', error);
    alert('Failed to approve campaign');
  }
};

const cancelCampaign = async (id: number) => {
  try {
    // await axios.post(`/api/admin/campaigns/${id}/cancel`);
    await refreshCampaigns();
  } catch (error) {
    console.error('Failed to cancel campaign:', error);
    alert('Failed to cancel campaign');
  }
};

const editCampaign = (id: number) => {
  router.push(`/admin/campaigns/${id}/edit`);
};

onMounted(async () => {
  await refreshCampaigns();
  // Fetch categories
  // const response = await axios.get('/api/categories');
  // categories.value = response.data;
});
</script> 