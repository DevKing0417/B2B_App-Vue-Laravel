<template>
  <div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <CurrencyDollarIcon class="h-6 w-6 text-gray-400" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Donations</dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">${{ stats.totalDonations.toLocaleString() }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <UserGroupIcon class="h-6 w-6 text-gray-400" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Active Campaigns</dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">{{ stats.activeCampaigns }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <UsersIcon class="h-6 w-6 text-gray-400" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Donors</dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">{{ stats.totalDonors }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <ChartBarIcon class="h-6 w-6 text-gray-400" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Avg. Donation</dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">${{ stats.averageDonation.toLocaleString() }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
      <!-- Donations Over Time -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Donations Over Time</h3>
        <div class="h-80">
          <canvas ref="donationsChart"></canvas>
        </div>
      </div>

      <!-- Campaign Distribution -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Campaign Distribution</h3>
        <div class="h-80">
          <canvas ref="campaignsChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Activity</h3>
      </div>
      <div class="border-t border-gray-200">
        <ul class="divide-y divide-gray-200">
          <li v-for="activity in recentActivity" :key="activity.id" class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <component
                    :is="activity.icon"
                    class="h-5 w-5 text-gray-400"
                  />
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">{{ activity.description }}</p>
                  <p class="text-sm text-gray-500">{{ formatDate(activity.created_at) }}</p>
                </div>
              </div>
              <div class="ml-4 flex-shrink-0">
                <span
                  :class="[
                    'px-2 py-1 text-xs font-semibold rounded-full',
                    activity.type === 'donation' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'
                  ]"
                >
                  {{ activity.type }}
                </span>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { format } from 'date-fns';
import {
  CurrencyDollarIcon,
  UserGroupIcon,
  UsersIcon,
  ChartBarIcon,
} from '@heroicons/vue/24/outline';
import Chart from 'chart.js/auto';

interface Activity {
  id: number;
  icon: any;
  description: string;
  created_at: string;
  type: 'donation' | 'campaign';
}

const stats = ref({
  totalDonations: 0,
  activeCampaigns: 0,
  totalDonors: 0,
  averageDonation: 0,
});

const recentActivity = ref<Activity[]>([]);
const donationsChart = ref<HTMLCanvasElement | null>(null);
const campaignsChart = ref<HTMLCanvasElement | null>(null);

const formatDate = (date: string) => {
  return format(new Date(date), 'MMM d, yyyy HH:mm');
};

const initCharts = () => {
  if (donationsChart.value) {
    new Chart(donationsChart.value, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: 'Donations',
          data: [12000, 19000, 15000, 25000, 22000, 30000],
          borderColor: 'rgb(79, 70, 229)',
          tension: 0.1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  }

  if (campaignsChart.value) {
    new Chart(campaignsChart.value, {
      type: 'doughnut',
      data: {
        labels: ['Education', 'Healthcare', 'Environment', 'Social'],
        datasets: [{
          data: [30, 25, 20, 25],
          backgroundColor: [
            'rgb(79, 70, 229)',
            'rgb(16, 185, 129)',
            'rgb(245, 158, 11)',
            'rgb(239, 68, 68)'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  }
};

onMounted(async () => {
  try {
    // Fetch dashboard data
    // const response = await axios.get('/api/admin/dashboard');
    // stats.value = response.data.stats;
    // recentActivity.value = response.data.recentActivity;

    // Initialize charts
    initCharts();
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error);
  }
});
</script> 