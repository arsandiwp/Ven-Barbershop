<template>
    <v-container grid-list-md pt-10>
        <HeaderBar />

        <!-- Loading state -->
        <v-row justify="center" v-if="loading">
            <v-progress-circular
                indeterminate
                color="primary"
                size="64"
            ></v-progress-circular>
        </v-row>

        <v-layout row wrap align-center v-else>
            <v-flex xs12 text-xs-center>
                <!-- Search Form -->
                <v-row>
                    <v-col>
                        <v-sheet color="white" elevation="2" rounded>
                            <v-form
                                @submit.prevent="searchRestaurants()"
                                ref="searchForm"
                            >
                                <v-container>
                                    <!-- Search Location -->
                                    <v-row>
                                        <v-col cols="12">
                                            <label
                                                for="location"
                                                class="text-label"
                                                >Search Restaurants*</label
                                            >
                                            <v-autocomplete
                                                v-model="selectedLocation"
                                                :items="locationItems"
                                                :loading="locationLoading"
                                                :search-input.sync="
                                                    locationQuery
                                                "
                                                label="Enter Location"
                                                item-text="fullName"
                                                item-value="locationId"
                                                return-object
                                                no-filter
                                                :rules="[
                                                    (v) =>
                                                        !!v ||
                                                        'Location is required',
                                                ]"
                                                @input="onLocationSelect"
                                                @click:clear="onLocationClear"
                                                clearable
                                            >
                                                <template
                                                    v-slot:item="{ item }"
                                                >
                                                    <v-list-item-content>
                                                        <v-list-item-title>{{
                                                            item.name
                                                        }}</v-list-item-title>
                                                        <v-list-item-subtitle>{{
                                                            item.fullName
                                                        }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </template>
                                            </v-autocomplete>
                                        </v-col>
                                    </v-row>

                                    <div class="text-center mt-10">
                                        <v-btn
                                            class="elevation-1"
                                            color="primary"
                                            type="submit"
                                            large
                                            :loading="isSearching"
                                            :disabled="!canSearch"
                                        >
                                            <v-icon left>mdi-magnify</v-icon>
                                            Search Restaurant
                                        </v-btn>
                                    </div>
                                </v-container>
                            </v-form>
                        </v-sheet>
                    </v-col>
                </v-row>

                <!-- No results message -->
                <v-row
                    v-if="
                        searchPerformed &&
                        !isSearching &&
                        restaurants.length === 0
                    "
                >
                    <v-col>
                        <v-alert type="info" prominent>
                            <v-row align="center">
                                <v-col class="grow">
                                    <div class="title">
                                        No restaurants found
                                    </div>
                                    <div>
                                        No restaurants found for your search
                                        criteria. Please try different dates or
                                        location.
                                    </div>
                                </v-col>
                            </v-row>
                        </v-alert>
                    </v-col>
                </v-row>

                <!-- Restaurant Results -->
                <!-- <v-row v-if="restaurants.length > 0">
                    <v-col>
                        <div class="text-left mb-4">
                            <h3>{{ totalResults }} restaurants found</h3>
                            <v-select
                                v-model="sortBy"
                                :items="sortOptions"
                                label="Sort by"
                                dense
                                style="max-width: 200px; display: inline-block"
                                @change="onSortChange"
                            ></v-select>
                        </div>
                    </v-col>
                </v-row> -->

                <v-row>
                    <v-col
                        v-for="restaurant in restaurants"
                        :key="restaurant.id"
                        cols="12"
                        sm="6"
                        md="4"
                        lg="3"
                    >
                        <v-card
                            hover
                            class="restaurant-card"
                            @click="viewRestaurantDetails(restaurant)"
                        >
                            <!-- Image -->
                            <v-img
                                height="200"
                                :src="getRestaurantImage(restaurant)"
                                :alt="restaurant.name"
                                cover
                            >
                                <template v-slot:placeholder>
                                    <v-row
                                        class="fill-height ma-0"
                                        align="center"
                                        justify="center"
                                    >
                                        <v-progress-circular
                                            indeterminate
                                            color="grey lighten-5"
                                        ></v-progress-circular>
                                    </v-row>
                                </template>
                            </v-img>

                            <!-- Open & Closed -->
                            <v-chip
                                class="mt-2"
                                small
                                v-if="restaurant.currentOpenStatusCategory"
                                :color="
                                    restaurant.currentOpenStatusCategory ===
                                    'OPEN'
                                        ? 'green'
                                        : 'red'
                                "
                            >
                                {{ restaurant.currentOpenStatusText }}
                            </v-chip>

                            <!-- Title -->
                            <v-card-title class="pb-2">
                                {{ restaurant.name }}
                            </v-card-title>

                            <v-card-subtitle
                                v-if="
                                    restaurant.establishmentTypeAndCuisineTags
                                "
                                class="pt-0"
                            >
                                {{
                                    restaurant.establishmentTypeAndCuisineTags.join(
                                        ", "
                                    )
                                }}
                            </v-card-subtitle>

                            <v-card-text class="pb-0">
                                <!-- Rating -->
                                <v-row
                                    align="center"
                                    class="mx-0"
                                    v-if="restaurant.averageRating > 0"
                                >
                                    <v-rating
                                        :value="restaurant.averageRating"
                                        color="amber"
                                        dense
                                        half-increments
                                        readonly
                                        size="14"
                                    ></v-rating>
                                    <div class="grey--text ms-2">
                                        {{ restaurant.averageRating }}
                                        ({{ restaurant.userReviewCount }})
                                    </div>
                                </v-row>

                                <!-- Menu -->
                                <div
                                    v-if="restaurant.menuUrl"
                                    class="text-caption grey--text mt-1"
                                >
                                    menu
                                    <a :href="restaurant.menuUrl">{{
                                        restaurant.menuUrl
                                    }}</a>
                                </div>
                            </v-card-text>

                            <v-card-actions>
                                <v-btn
                                    text
                                    color="primary"
                                    @click.stop="
                                        viewRestaurantDetails(restaurant)
                                    "
                                >
                                    View Details
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Load More Button -->
                <!-- <v-row v-if="canLoadMore">
                    <v-col class="text-center">
                        <v-btn
                            color="primary"
                            outlined
                            @click="loadMoreHotels"
                            :loading="loadingMore"
                        >
                            Load More Restaurants
                        </v-btn>
                    </v-col>
                </v-row> -->
            </v-flex>
        </v-layout>

        <!-- Error Snackbar -->
        <v-snackbar v-model="showError" color="error" timeout="6000">
            {{ errorMessage }}
            <template v-slot:action="{ attrs }">
                <v-btn text v-bind="attrs" @click="showError = false">
                    Close
                </v-btn>
            </template>
        </v-snackbar>
    </v-container>
</template>

<script>
import { debounce } from "lodash";
import axios from "axios";

export default {
    name: "HotelSearch",
    data() {
        return {
            loading: false,
            isSearching: false,
            loadingMore: false,
            locationLoading: false,
            searchPerformed: false,
            showError: false,
            errorMessage: "",

            // Location search
            locationQuery: "",
            locationItems: [],
            selectedLocation: null,

            searchParams: {
                page: 1,
            },

            // Results
            restaurants: [],
            totalResults: 0,
            canLoadMore: false,
        };
    },
    computed: {
        canSearch() {
            return this.selectedLocation;
        },
    },
    watch: {
        locationQuery: {
            handler: debounce(function (newVal) {
                // Hanya search jika belum ada selectedLocation atau query berbeda dari selection
                if (newVal && newVal.length >= 2) {
                    // Jangan search jika query sama dengan nama location yang sudah dipilih
                    if (
                        this.selectedLocation &&
                        (newVal === this.selectedLocation.fullName ||
                            newVal === this.selectedLocation.name)
                    ) {
                        return;
                    }
                    this.searchLocations(newVal);
                } else if (!newVal || newVal.length === 0) {
                    this.locationItems = [];
                    if (this.selectedLocation) {
                        this.selectedLocation = null;
                    }
                }
            }, 300),
        },
        // Tambahkan watcher untuk selectedLocation
        selectedLocation(newVal) {
            if (newVal) {
                this.locationQuery = newVal.fullName || newVal.name;
            }
        },
    },
    created() {},
    methods: {
        async searchLocations(query) {
            if (!query || query.length < 2) return;

            this.locationLoading = true;
            try {
                const response = await axios.get(this.API + "/locations", {
                    params: { query },
                });

                console.log("ini repon guys", response.data);

                if (response.data.status === "success") {
                    this.locationItems = response.data.locations || [];
                } else {
                    this.showErrorMessage("Failed to search locations");
                }
            } catch (error) {
                console.error("Location search error:", error);
                this.showErrorMessage("Error searching locations");
            } finally {
                this.locationLoading = false;
            }
        },

        onLocationSelect(location) {
            if (location) {
                this.selectedLocation = location;
                // Reset locationQuery agar tidak ada konflik
                this.locationQuery = location.fullName || location.name;
                console.log("ini locationId", this.selectedLocation);
            }
        },

        onLocationClear() {
            this.selectedLocation = null;
            this.locationQuery = "";
            this.locationItems = [];
        },

        async searchRestaurants() {
            if (!this.canSearch) return;

            this.isSearching = true;
            this.searchPerformed = true;
            this.restaurants = [];
            this.searchParams.page = 1;

            try {
                const params = {
                    locationId: this.selectedLocation.locationId,
                    page: this.searchParams.page,
                };

                console.log("ini params guys", params);

                const response = await axios.get(this.API + "/restaurant", {
                    params,
                });

                console.log("ini respon restaurant", response);

                this.restaurants = response.data.data.data || [];
                this.totalResults = response.data.data.data.length || 0;
                // this.canLoadMore = this.restaurants.length >= 20; // Assuming 20 per page

                if (this.selectedLocation) {
                    this.locationQuery =
                        this.selectedLocation.fullName ||
                        this.selectedLocation.name;
                }
            } catch (error) {
                console.error("Restaurant search error:", error);
                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.message
                ) {
                    this.showErrorMessage(error.response.data.message);
                } else {
                    this.showErrorMessage(
                        "Error searching restaurants. Please try again."
                    );
                }
            } finally {
                this.isSearching = false;
            }
        },

        // async loadMoreHotels() {
        //     if (!this.canLoadMore || this.loadingMore) return;

        //     this.loadingMore = true;
        //     this.searchParams.page += 1;

        //     try {
        //         const params = {
        //             geoId: this.selectedLocation.locationId,
        //             checkIn: this.checkInDate,
        //             checkOut: this.checkOutDate,
        //             adults: this.searchParams.adults,
        //             children: this.searchParams.children,
        //             rooms: this.searchParams.rooms,
        //             currency: this.searchParams.currency,
        //             sort: this.searchParams.sort,
        //             page: this.searchParams.page,
        //         };

        //         const response = await axios.post(
        //             this.API + "/restaurants/search",
        //             params
        //         );

        //         if (response.data.status === "success") {
        //             const newHotels = response.data.data.restaurants || [];
        //             this.restaurants.push(...newHotels);
        //             this.canLoadMore = newHotels.length >= 20;
        //         } else {
        //             this.showErrorMessage("Failed to load more restaurants");
        //         }
        //     } catch (error) {
        //         console.error("Load more restaurants error:", error);
        //         this.showErrorMessage("Error loading more restaurants");
        //     } finally {
        //         this.loadingMore = false;
        //     }
        // },

        // onSortChange() {
        //     this.searchParams.sort = this.sortBy;
        //     if (this.restaurants.length > 0) {
        //         this.searchRestaurants();
        //     }
        // },

        getRestaurantImage(restaurant) {
            if (restaurant.imageUrl) {
                return restaurant.imageUrl;
            }

            const photo = restaurant.thumbnail?.photo?.photoSizeDynamic;
            if (photo?.urlTemplate) {
                return photo.urlTemplate
                    .replace("{width}", "400")
                    .replace("{height}", "300");
            }

            return "/api/placeholder/400/300";
        },

        viewRestaurantDetails(restaurant) {
            // Navigate to restaurant details page or open modal
            // this.$router.push({
            //     name: "HotelDetails",
            //     params: {
            //         id: restaurant.id,
            //     },
            //     query: {
            //         checkIn: this.checkInDate,
            //         checkOut: this.checkOutDate,
            //     },
            // });

            this.alert("On Develop");
        },

        showErrorMessage(message) {
            this.errorMessage = message;
            this.showError = true;
        },
    },
};
</script>

<style scoped>
.text-label {
    font-weight: 600;
    font-size: 14px;
    color: #424242;
    margin-bottom: 8px;
    display: block;
}

.restaurant-card {
    transition: transform 0.2s ease-in-out;
    cursor: pointer;
}

.restaurant-card:hover {
    transform: translateY(-2px);
}

.v-card__title {
    line-height: 1.2;
    font-size: 1.1rem;
}

.v-card__subtitle {
    font-size: 0.9rem;
    opacity: 0.8;
}

.v-progress-circular {
    margin: 1rem;
}

/* Responsive adjustments */
@media (max-width: 600px) {
    .v-container {
        padding: 8px;
    }

    .restaurant-card {
        margin-bottom: 16px;
    }
}
</style>
