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
                                                >Search Attractions*</label
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

                                    <!-- Additional Options -->
                                    <!-- <v-row>
                                        <v-col cols="12" md="4">
                                            <v-select
                                                v-model="searchParams.rooms"
                                                :items="roomOptions"
                                                label="Rooms"
                                                dense
                                            ></v-select>
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <v-select
                                                v-model="searchParams.adults"
                                                :items="adultOptions"
                                                label="Adults"
                                                dense
                                            ></v-select>
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <v-select
                                                v-model="searchParams.children"
                                                :items="childrenOptions"
                                                label="Children"
                                                dense
                                            ></v-select>
                                        </v-col>
                                    </v-row> -->

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
                                            Search Attractions
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
                        attractions.length === 0
                    "
                >
                    <v-col>
                        <v-alert type="info" prominent>
                            <v-row align="center">
                                <v-col class="grow">
                                    <div class="title">
                                        No attractions found
                                    </div>
                                    <div>
                                        No attractions found for your search
                                        criteria. Please try different dates or
                                        location.
                                    </div>
                                </v-col>
                            </v-row>
                        </v-alert>
                    </v-col>
                </v-row>

                <!-- Attraction Results -->
                <!-- <v-row v-if="attractions.length > 0">
                    <v-col>
                        <div class="text-left mb-4">
                            <h3>{{ totalResults }} attractions found</h3>
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
                        v-for="attraction in attractions"
                        :key="attraction.id"
                        cols="12"
                        sm="6"
                        md="4"
                        lg="3"
                    >
                        <v-card
                            hover
                            class="attraction-card"
                            @click="viewHotelDetails(attraction)"
                        >
                            <v-img
                                height="200"
                                :src="getHotelImage(attraction)"
                                :alt="attraction.nama"
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
                                v-if="attraction.status.teks"
                                color="primary"
                            >
                                {{ attraction.status.teks }}
                            </v-chip>

                            <v-card-title class="pb-2">
                                {{ attraction.nama }}
                            </v-card-title>

                            <v-card-subtitle
                                v-if="attraction.kategori.utama"
                                class="pt-0"
                            >
                                {{ attraction.kategori.utama }}
                            </v-card-subtitle>

                            <v-card-text class="pb-0">
                                <!-- Rating -->
                                <v-row
                                    align="center"
                                    class="mx-0"
                                    v-if="attraction.rating.nilai > 0"
                                >
                                    <v-rating
                                        :value="attraction.rating.nilai"
                                        color="amber"
                                        dense
                                        half-increments
                                        readonly
                                        size="14"
                                    ></v-rating>
                                    <div class="grey--text ms-2">
                                        {{ attraction.rating.nilai }}
                                        {{ attraction.rating.teks_ulasan }}
                                    </div>
                                </v-row>
                            </v-card-text>

                            <v-card-actions>
                                <v-btn
                                    text
                                    color="primary"
                                    @click.stop="viewHotelDetails(attraction)"
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
                            Load More Attractions
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
                page: 1,
            },

            // Results
            attractions: [],
            totalResults: 0,
            canLoadMore: false,            
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
            this.attractions = [];
            this.searchParams.page = 1;

            try {
                const params = {
                    geoId: this.selectedLocation.locationId,
                    startDate: this.checkInDate,
                    endDate: this.checkOutDate,
                };

                console.log("ini params guys", params);

                const response = await axios.get(this.API + "/attractions", {
                    params,
                });

                console.log("ini respon attraction", response);

                this.attractions = response.data.data.atraksi || [];
                this.totalResults = response.data.data.total_atraksi || 0;

                // Simpan query terakhir untuk mencegah hilang setelah search
                if (this.selectedLocation) {
                    this.locationQuery =
                        this.selectedLocation.fullName ||
                        this.selectedLocation.name;
                }
            } catch (error) {
                console.error("Attraction search error:", error);
                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.message
                ) {
                    this.showErrorMessage(error.response.data.message);
                } else {
                    this.showErrorMessage(
                        "Error searching attractions. Please try again."
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
        //             this.API + "/attractions/search",
        //             params
        //         );

        //         if (response.data.status === "success") {
        //             const newHotels = response.data.data.attractions || [];
        //             this.attractions.push(...newHotels);
        //             this.canLoadMore = newHotels.length >= 20;
        //         } else {
        //             this.showErrorMessage("Failed to load more attractions");
        //         }
        //     } catch (error) {
        //         console.error("Load more attractions error:", error);
        //         this.showErrorMessage("Error loading more attractions");
        //     } finally {
        //         this.loadingMore = false;
        //     }
        // },

        // onSortChange() {
        //     this.searchParams.sort = this.sortBy;
        //     if (this.attractions.length > 0) {
        //         this.searchHotels();
        //     }
        // },

        getHotelImage(attraction) {
            if (attraction.foto && attraction.foto.url_template) {
                return attraction.foto.url_template
                    .replace("{width}", "400")
                    .replace("{height}", "300");
            }
            return "/api/placeholder/400/300";
        },

        viewHotelDetails(attraction) {
            // Navigate to attraction details page or open modal
            // this.$router.push({
            //     name: "HotelDetails",
            //     params: {
            //         id: attraction.id,
            //     },
            //     query: {
            //         checkIn: this.checkInDate,
            //         checkOut: this.checkOutDate,
            //     },
            // });

            this.alert("On Develop");
        },

        // bookHotel(attraction) {
        //     if (
        //         attraction.commerceInfo &&
        //         attraction.commerceInfo.externalUrl
        //     ) {
        //         window.open(attraction.commerceInfo.externalUrl, "_blank");
        //     }
        // },

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

.attraction-card {
    transition: transform 0.2s ease-in-out;
    cursor: pointer;
}

.attraction-card:hover {
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

    .attraction-card {
        margin-bottom: 16px;
    }
}
</style>
