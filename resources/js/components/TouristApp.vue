<template>
    <div class="min-h-screen bg-blue-50 flex flex-col">
      <!-- Header -->
      <header class="bg-blue-600 text-white py-4 text-center shadow-lg">
        <h1 class="text-3xl font-bold">Tourist Weather & Location App</h1>
      </header>

      <!-- Main Content -->
      <div class="flex-grow flex items-center justify-center p-4">
        <!-- Weather Card -->
        <div class="max-w-lg w-full bg-white bg-opacity-20 backdrop-blur-lg rounded-lg shadow-lg border border-white border-opacity-30 overflow-hidden mr-4">
          <!-- Search Field Section -->
          <div class="px-4 py-2">
            <div class="flex items-center relative">
              <select v-model="selectedCity" @change="fetchWeather" class="w-full p-2 rounded-lg bg-gray-200 border border-gray-400">
                <option disabled value="">Select a city</option>
                <option v-for="city in cities" :key="city.name" :value="city.name">{{ city.name }}</option>
              </select>
            </div>
          </div>

          <!-- Current Weather Section -->
          <div class="px-4 py-4 text-blue-900 flex flex-col items-center">
            <h2 class="text-2xl font-bold text-center">{{ location.name }}</h2>
            <p class="text-5xl font-semibold mt-1">{{ currentTemperature.temperature }}°C</p>
            <p class="text-md text-center">Feels like {{ currentTemperature.feels }}°C</p>
            <img :src="`https://openweathermap.org/img/wn/${currentTemperature.icon}.png`" alt="Weather Icon" class="w-20 h-20 my-2">
            <p class="text-center text-md">{{ currentTemperature.description }}</p>
          </div>

          <!-- Divider -->
          <div class="h-0.5 bg-gray-300 opacity-30"></div>

          <!-- Future Weather Section -->
          <div class="px-4 py-2 grid grid-cols-3 gap-2 text-blue-900">
            <div v-for="(day, index) in daily" :key="index" class="bg-white bg-opacity-30 p-2 rounded-md shadow-md text-center">
              <p class="text-sm font-semibold">{{ convertTimestampToAbbreviatedDay(day.dt) }}</p>
              <img :src="`https://openweathermap.org/img/wn/${day.weather[0].icon}.png`" alt="Weather Icon" class="w-10 h-10 mx-auto my-1">
              <p class="text-xs">Max: {{ Math.round(day.main.temp_max - 273.15) }}°C</p>
              <p class="text-xs">Min: {{ Math.round(day.main.temp_min - 273.15) }}°C</p>
            </div>
          </div>
        </div>

        <!-- Tourist Spots Table -->
        <div class="w-full max-w-md bg-white bg-opacity-20 backdrop-blur-lg rounded-lg shadow-lg border border-white border-opacity-30 p-4">
          <h3 class="text-lg font-bold text-blue-900 mb-4 text-center">Tourist Spots</h3>
          <table class="min-w-full text-left text-sm text-gray-600">
            <thead class="text-xs uppercase bg-blue-600">
              <tr>
                <th class="py-2 px-4 text-white">Spot Name</th>
                <th class="py-2 px-4 text-white">Description</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(spot, index) in touristSpots" :key="index" class="bg-blue-50 hover:bg-blue-100">
                <td class="py-2 px-4 text-blue-900">{{ spot.name }}</td>
                <td class="py-2 px-4 text-blue-900">{{ spot.description }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Footer -->
      <footer class="bg-blue-600 text-white py-2 text-center shadow-lg">
        <p>&copy; 2024 Tourist Weather & Location App. All rights reserved.</p>
      </footer>
    </div>
  </template>

  <script>
  import axios from 'axios';

  export default {
    data() {
      return {
        currentTemperature: {
          temperature: '',
          feels: '',
          description: '',
          icon: '',
        },
        daily: [],
        location: {
          name: '',
          lat: '',
          lon: '',
        },
        selectedCity: 'Tokyo', // Set default to Tokyo
        cities: [ // Cities for dropdown
          { name: "Tokyo" },
          { name: "Yokohama" },
          { name: "Kyoto" },
          { name: "Osaka" },
          { name: "Sapporo" },
          { name: "Nagoya" },
        ],
        touristSpots: [],
        touristSpotsData: { // Mapping tourist spots to cities
          Tokyo: [
            { name: "Tokyo Disneyland", description: "A magical theme park." },
            { name: "Urayasu Fish Market", description: "Fresh seafood and local delicacies." },
            { name: "Shoppes at Maihama", description: "Shopping and dining destination." },
            { name: "Urayasu City Museum", description: "Learn about the history and culture." },
            { name: "Tokyo Bay", description: "Beautiful waterfront area." },
          ],
          Yokohama: [
            { name: "Yokohama Chinatown", description: "The largest Chinatown in Japan." },
            { name: "Yokohama Landmark Tower", description: "Iconic skyscraper with stunning views." },
            { name: "Sankeien Garden", description: "Traditional Japanese garden." },
            { name: "Cup Noodles Museum", description: "Interactive museum on instant noodles." },
          ],
          Kyoto: [
            { name: "Kinkaku-ji", description: "Golden Pavilion and UNESCO World Heritage Site." },
            { name: "Fushimi Inari-taisha", description: "Famous for its thousands of torii gates." },
            { name: "Arashiyama Bamboo Grove", description: "A stunning bamboo forest." },
          ],
          Osaka: [
            { name: "Universal Studios Japan", description: "Famous theme park." },
            { name: "Dotonbori", description: "Vibrant district known for street food." },
          ],
          Sapporo: [
            { name: "Sapporo Snow Festival", description: "Annual festival featuring ice sculptures." },
            { name: "Odori Park", description: "Central park in Sapporo." },
          ],
          Nagoya: [
            { name: "Nagoya Castle", description: "Historic castle with beautiful gardens." },
            { name: "Atsuta Shrine", description: "Important Shinto shrine." },
          ],
        },
      };
    },
    mounted() {
      // Fetch weather data for Tokyo on mount
      this.fetchWeather();
    },
    methods: {
      fetchWeather() {
        // Set location name based on selected city
        this.location.name = this.selectedCity;

        // Update tourist spots based on selected city
        this.touristSpots = this.touristSpotsData[this.selectedCity] || [];

        // I Just Static the data for the meantime due to Foursquare api showing different locations :(
        const cityCoordinates = {
          Tokyo: { lat: 35.682839, lon: 139.759455 },
          Yokohama: { lat: 35.443708, lon: 139.637262 },
          Kyoto: { lat: 35.011636, lon: 135.768029 },
          Osaka: { lat: 34.693738, lon: 135.502165 },
          Sapporo: { lat: 43.061814, lon: 141.354506 },
          Nagoya: { lat: 35.181446, lon: 136.906398 },
        };

        const { lat, lon } = cityCoordinates[this.selectedCity];

        fetch(`/api/weather?lat=${lat}&lon=${lon}`)
          .then((response) => response.json())
          .then((data) => {
            const mainData = data.list[0].main;
            const weatherData = data.list[0].weather[0];

            this.currentTemperature.temperature = Math.round(mainData.temp - 273.15);
            this.currentTemperature.feels = Math.round(mainData.feels_like - 273.15);
            this.currentTemperature.description = weatherData.description;
            this.currentTemperature.icon = weatherData.icon;

            this.daily = data.list.filter((_, index) => index % 8 === 0);
          })
          .catch(error => {
            console.error('Error fetching weather data:', error);
          });
      },
      convertTimestampToAbbreviatedDay(timestamp) {
        const date = new Date(timestamp * 1000);
        return new Intl.DateTimeFormat('en-US', { weekday: 'short' }).format(date).toUpperCase();
      },
    },
  };
  </script>
