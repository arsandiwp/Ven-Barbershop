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
                                @submit.prevent="searchHotels()"
                                ref="searchForm"
                            >
                                <v-container>
                                    <!-- Search Location -->
                                    <v-row>
                                        <v-col cols="12">
                                            <label
                                                for="location"
                                                class="text-label"
                                                >Search Hotels*</label
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

                                    <v-row>
                                        <!-- Check In -->
                                        <v-col cols="12" md="6">
                                            <v-menu
                                                v-model="checkInMenu"
                                                :close-on-content-click="false"
                                                :nudge-right="40"
                                                transition="scale-transition"
                                                offset-y
                                                min-width="auto"
                                            >
                                                <template
                                                    v-slot:activator="{
                                                        on,
                                                        attrs,
                                                    }"
                                                >
                                                    <label
                                                        for="checkIn"
                                                        class="text-label"
                                                        >Check In*</label
                                                    >
                                                    <v-text-field
                                                        v-model="
                                                            checkInFormatted
                                                        "
                                                        placeholder="Select Check In Date"
                                                        prepend-icon="mdi-calendar"
                                                        readonly
                                                        v-bind="attrs"
                                                        v-on="on"
                                                        :rules="[
                                                            (v) =>
                                                                !!v ||
                                                                'Check in date is required',
                                                        ]"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    v-model="checkInDate"
                                                    :min="minCheckInDate"
                                                    @input="checkInMenu = false"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>

                                        <!-- Check Out -->
                                        <v-col cols="12" md="6">
                                            <v-menu
                                                v-model="checkOutMenu"
                                                :close-on-content-click="false"
                                                :nudge-right="40"
                                                transition="scale-transition"
                                                offset-y
                                                min-width="auto"
                                            >
                                                <template
                                                    v-slot:activator="{
                                                        on,
                                                        attrs,
                                                    }"
                                                >
                                                    <label
                                                        for="checkOut"
                                                        class="text-label"
                                                        >Check Out*</label
                                                    >
                                                    <v-text-field
                                                        v-model="
                                                            checkOutFormatted
                                                        "
                                                        placeholder="Select Check Out Date"
                                                        prepend-icon="mdi-calendar"
                                                        readonly
                                                        v-bind="attrs"
                                                        v-on="on"
                                                        :rules="[
                                                            (v) =>
                                                                !!v ||
                                                                'Check out date is required',
                                                        ]"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    v-model="checkOutDate"
                                                    :min="minCheckOutDate"
                                                    @input="
                                                        checkOutMenu = false
                                                    "
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <!-- Room -->
                                        <v-col cols="12" md="4">
                                            <v-select
                                                v-model="searchParams.rooms"
                                                :items="roomOptions"
                                                label="Rooms"
                                                dense
                                            ></v-select>
                                        </v-col>

                                        <!-- Adults -->
                                        <v-col cols="12" md="4">
                                            <v-select
                                                v-model="searchParams.adults"
                                                :items="adultOptions"
                                                label="Adults"
                                                dense
                                            ></v-select>
                                        </v-col>

                                        <!-- Children -->
                                        <v-col cols="12" md="4">
                                            <v-select
                                                v-model="searchParams.children"
                                                :items="childrenOptions"
                                                label="Children"
                                                dense
                                            ></v-select>
                                        </v-col>
                                    </v-row>

                                    <!-- Button Search Hotels -->
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
                                            Search Hotels
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
                        searchPerformed && !isSearching && hotels.length === 0
                    "
                >
                    <v-col>
                        <v-alert type="info" prominent>
                            <v-row align="center">
                                <v-col class="grow">
                                    <div class="title">No hotels found</div>
                                    <div>
                                        No hotels found for your search
                                        criteria. Please try different dates or
                                        location.
                                    </div>
                                </v-col>
                            </v-row>
                        </v-alert>
                    </v-col>
                </v-row>

                <!-- Hotel Results -->
                <!-- <v-row v-if="hotels.length > 0">
                    <v-col>
                        <div class="text-left mb-4">
                            <h3>{{ totalResults }} hotels found</h3>
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
                        v-for="hotel in hotels"
                        :key="hotel.id"
                        cols="12"
                        sm="6"
                        md="4"
                        lg="3"
                    >
                        <v-card
                            hover
                            class="hotel-card"
                            @click="viewHotelDetails(hotel)"
                        >
                            <v-img
                                height="200"
                                :src="getHotelImage(hotel)"
                                :alt="hotel.title"
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

                            <!-- Badge -->
                            <v-chip
                                class="mt-2"
                                small
                                v-if="hotel.badge.type"
                                color="primary"
                            >
                                {{ hotel.badge.type }}
                            </v-chip>

                            <v-card-title class="pb-2">
                                {{ hotel.title }}
                            </v-card-title>

                            <v-card-subtitle
                                v-if="hotel.secondaryInfo"
                                class="pt-0"
                            >
                                {{ hotel.secondaryInfo }}
                            </v-card-subtitle>

                            <v-card-text class="pb-0">
                                <v-row
                                    align="center"
                                    class="mx-0"
                                    v-if="hotel.bubbleRating.rating > 0"
                                >
                                    <v-rating
                                        :value="hotel.bubbleRating.rating"
                                        color="amber"
                                        dense
                                        half-increments
                                        readonly
                                        size="14"
                                    ></v-rating>
                                    <div class="grey--text ms-2">
                                        {{ hotel.bubbleRating.rating }}
                                        {{ hotel.bubbleRating.count }}
                                    </div>
                                </v-row>

                                <!-- Price -->
                                <div class="mt-2" v-if="hotel.priceForDisplay">
                                    <span
                                        class="text-h6 primary--text font-weight-bold"
                                    >
                                        {{ hotel.priceForDisplay }}
                                    </span>
                                    <span class="text-caption grey--text ml-1"
                                        >per night</span
                                    >
                                    <div
                                        v-if="hotel.priceDetails"
                                        class="text-caption grey--text"
                                    >
                                        {{ hotel.priceDetails }}
                                    </div>
                                </div>

                                <div
                                    v-if="hotel.provider"
                                    class="text-caption grey--text mt-1"
                                >
                                    via {{ hotel.provider }}
                                </div>
                            </v-card-text>

                            <v-card-actions>
                                <v-btn
                                    text
                                    color="primary"
                                    @click.stop="viewHotelDetails(hotel)"
                                >
                                    View Details
                                </v-btn>
                                <v-spacer></v-spacer>
                                <v-btn
                                    v-if="hotel.commerceInfo"
                                    color="primary"
                                    @click.stop="bookHotel(hotel)"
                                    small
                                >
                                    Book Now
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
                            Load More Hotels
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

            // Date selection
            checkInDate: null,
            checkOutDate: null,
            checkInMenu: false,
            checkOutMenu: false,

            // Search parameters
            searchParams: {
                adults: 1,
                children: 0,
                rooms: 1,
                // currency: "USD",
                // sort: "BEST_VALUE",
                page: 1,
            },

            // Results
            hotels: [],
            totalResults: 0,
            canLoadMore: false,
            sortBy: "BEST_VALUE",

            // Options
            roomOptions: [
                { text: "1 Room", value: 1 },
                { text: "2 Rooms", value: 2 },
                { text: "3 Rooms", value: 3 },
                { text: "4 Rooms", value: 4 },
                { text: "5 Rooms", value: 5 },
            ],
            adultOptions: [
                { text: "1 Adult", value: 1 },
                { text: "2 Adults", value: 2 },
                { text: "3 Adults", value: 3 },
                { text: "4 Adults", value: 4 },
                { text: "5 Adults", value: 5 },
                { text: "6 Adults", value: 6 },
                { text: "7 Adults", value: 7 },
                { text: "8 Adults", value: 8 },
            ],
            childrenOptions: [
                { text: "No Children", value: 0 },
                { text: "1 Child", value: 1 },
                { text: "2 Children", value: 2 },
                { text: "3 Children", value: 3 },
                { text: "4 Children", value: 4 },
                { text: "5 Children", value: 5 },
                { text: "6 Children", value: 6 },
            ],
            // sortOptions: [
            //     { text: "Best Value", value: "BEST_VALUE" },
            //     { text: "Price: Low to High", value: "PRICE_LOW_TO_HIGH" },
            //     { text: "Price: High to Low", value: "PRICE_HIGH_TO_LOW" },
            //     { text: "Rating: High to Low", value: "RATING_HIGH_TO_LOW" },
            //     { text: "Popularity", value: "POPULARITY" },
            //     { text: "Distance", value: "DISTANCE" },
            // ],
        };
    },
    computed: {
        minCheckInDate() {
            return new Date().toISOString().substr(0, 10);
        },
        minCheckOutDate() {
            if (this.checkInDate) {
                const checkIn = new Date(this.checkInDate);
                checkIn.setDate(checkIn.getDate() + 1);
                return checkIn.toISOString().substr(0, 10);
            }
            return this.minCheckInDate;
        },
        checkInFormatted() {
            if (this.checkInDate) {
                return new Date(this.checkInDate).toLocaleDateString("en-US", {
                    weekday: "short",
                    year: "numeric",
                    month: "short",
                    day: "numeric",
                });
            }
            return "";
        },
        checkOutFormatted() {
            if (this.checkOutDate) {
                return new Date(this.checkOutDate).toLocaleDateString("en-US", {
                    weekday: "short",
                    year: "numeric",
                    month: "short",
                    day: "numeric",
                });
            }
            return "";
        },
        canSearch() {
            return (
                this.selectedLocation && this.checkInDate && this.checkOutDate
            );
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

        checkInDate(newVal) {
            if (newVal && this.checkOutDate && newVal >= this.checkOutDate) {
                const nextDay = new Date(newVal);
                nextDay.setDate(nextDay.getDate() + 1);
                this.checkOutDate = nextDay.toISOString().substr(0, 10);
            }
        },

        // Tambahkan watcher untuk selectedLocation
        selectedLocation(newVal) {
            if (newVal) {
                this.locationQuery = newVal.fullName || newVal.name;
            }
        },
    },
    created() {
        // Set default dates
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const dayAfter = new Date(today);
        dayAfter.setDate(dayAfter.getDate() + 2);

        this.checkInDate = tomorrow.toISOString().substr(0, 10);
        this.checkOutDate = dayAfter.toISOString().substr(0, 10);
    },
    methods: {
        async searchLocations(query) {
            if (!query || query.length < 2) {
                this.locationItems = [];
                return;
            }

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

        async searchHotels() {
            if (!this.canSearch) return;

            this.isSearching = true;
            this.searchPerformed = true;
            this.hotels = [];
            this.searchParams.page = 1;

            try {
                const params = {
                    geoId: this.selectedLocation.locationId,
                    checkIn: this.checkInDate,
                    checkOut: this.checkOutDate,
                    adults: this.searchParams.adults,
                    children: this.searchParams.children,
                    rooms: this.searchParams.rooms,
                    // currency: this.searchParams.currency,
                    // sort: this.searchParams.sort,
                    page: this.searchParams.page,
                };

                console.log("ini params guys", params);

                const response = await axios.get(this.API + "/hotels", {
                    params,
                });

                console.log("ini respon hotel", response.data.data.data);

                this.hotels = response.data.data.data || [];
                this.totalResults = response.data.data.data.length || 0;
                // this.canLoadMore = this.hotels.length >= 20; // Assuming 20 per page

                if (this.selectedLocation) {
                    this.locationQuery =
                        this.selectedLocation.fullName ||
                        this.selectedLocation.name;
                }
            } catch (error) {
                console.error("Hotel search error:", error);
                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.message
                ) {
                    this.showErrorMessage(error.response.data.message);
                } else {
                    this.showErrorMessage(
                        "Error searching hotels. Please try again."
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
        //             // currency: this.searchParams.currency,
        //             // sort: this.searchParams.sort,
        //             page: this.searchParams.page,
        //         };

        //         const response = await axios.get(this.API + "/hotels", {
        //             params,
        //         });

        //         const newHotels = response.data.data.data || [];
        //         this.hotels.push(...newHotels);
        //         this.canLoadMore = this.hotels.length >= 20;
        //     } catch (error) {
        //         console.error("Load more hotels error:", error);
        //         this.showErrorMessage("Error loading more hotels");
        //     } finally {
        //         this.loadingMore = false;
        //     }
        // },

        // onSortChange() {
        //     this.searchParams.sort = this.sortBy;
        //     if (this.hotels.length > 0) {
        //         this.searchHotels();
        //     }
        // },

        getHotelImage(hotel) {
            if (hotel.imageUrl) {
                return hotel.imageUrl;
            }
            if (hotel.cardPhotos && hotel.cardPhotos.length > 0) {
                const photo = hotel.cardPhotos[0];
                if (photo.sizes && photo.sizes.urlTemplate) {
                    return photo.sizes.urlTemplate
                        .replace("{width}", "400")
                        .replace("{height}", "300");
                }
            }
            return "/api/placeholder/400/300";
        },

        viewHotelDetails(hotel) {
            // Navigate to hotel details page or open modal
            // this.$router.push({
            //     name: "HotelDetails",
            //     params: {
            //         id: hotel.id,
            //     },
            //     query: {
            //         checkIn: this.checkInDate,
            //         checkOut: this.checkOutDate,
            //     },
            // });

            this.alert("On Develop");
        },

        bookHotel(hotel) {
            if (hotel.commerceInfo && hotel.commerceInfo.externalUrl) {
                window.open(hotel.commerceInfo.externalUrl, "_blank");
            }
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

.hotel-card {
    transition: transform 0.2s ease-in-out;
    cursor: pointer;
}

.hotel-card:hover {
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

    .hotel-card {
        margin-bottom: 16px;
    }
}
</style>
