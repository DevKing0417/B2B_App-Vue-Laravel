<template>
  <div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">{{ campaign.title }}</h3>
        <span
          :class="[
            'px-2 py-1 text-xs font-semibold rounded-full',
            campaign.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
          ]"
        >
          {{ campaign.status }}
        </span>
      </div>
      
      <p class="mt-2 text-sm text-gray-600">{{ campaign.description }}</p>
      
      <div class="mt-4">
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-500">Target Amount</span>
          <span class="font-medium text-gray-900">${{ campaign.target_amount.toLocaleString() }}</span>
        </div>
        <div class="mt-2">
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-indigo-600 h-2 rounded-full"
              :style="{ width: `${(campaign.current_amount / campaign.target_amount) * 100}%` }"
            ></div>
          </div>
        </div>
        <div class="mt-2 flex items-center justify-between text-sm">
          <span class="text-gray-500">Raised</span>
          <span class="font-medium text-gray-900">${{ campaign.current_amount.toLocaleString() }}</span>
        </div>
      </div>

      <div class="mt-6 flex items-center justify-between">
        <div class="flex items-center text-sm text-gray-500">
          <CalendarIcon class="h-4 w-4 mr-1" />
          <span>Ends {{ formatDate(campaign.end_date) }}</span>
        </div>
        <router-link
          :to="`/campaigns/${campaign.id}`"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          View Details
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { CalendarIcon } from '@heroicons/vue/24/outline';
import { format } from 'date-fns';

interface Campaign {
  id: number;
  title: string;
  description: string;
  target_amount: number;
  current_amount: number;
  status: 'active' | 'completed' | 'cancelled';
  end_date: string;
}

defineProps<{
  campaign: Campaign;
}>();

const formatDate = (date: string) => {
  return format(new Date(date), 'MMM d, yyyy');
};
</script> 