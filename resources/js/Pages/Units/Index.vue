<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-dash-title">Projects</h2>
        <button v-if="can('units.create')" @click="openCreate" class="bg-black text-white px-3 py-2 rounded hover:bg-gray-700">
          + Add Project
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-white p-4 rounded-xl shadow-sm ring-1 ring-black/5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <input v-model.trim="filters.q" type="text" placeholder="Search by name or location…" class="form-input" />
          <select v-model="filters.status" class="form-input">
            <option value="">All statuses</option>
            <option value="under_construction">under_construction</option>
            <option value="completed">completed</option>
          </select>
          <input v-model="filters.from" type="date" class="form-input" />
          <input v-model="filters.to" type="date" class="form-input" />
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-xl shadow-sm ring-1 ring-black/5 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-4 py-2 text-left"></th>
              <th class="px-4 py-2 text-left">Name (EN / AR)</th>
              <th class="px-4 py-2 text-left">Location</th>
              <th class="px-4 py-2 text-left">Period</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="6" class="px-4 py-6 text-center text-gray-500">Loading…</td>
            </tr>

            <tr v-for="u in paginated" :key="u.id" class="border-t">
              <td class="px-4 py-2"></td>
              <td class="px-4 py-2">
                <div class="font-medium text-gray-900">{{ u.name_en || '—' }}</div>
                <div class="text-gray-500" dir="rtl">{{ u.name_ar }}</div>
              </td>
              <td class="px-4 py-2">
                <div class="text-gray-900">{{ u.location || u.address || '—' }}</div>
                <div v-if="u.lat && u.long" class="text-xs text-gray-500">({{ u.lat }}, {{ u.long }})</div>
              </td>
              <td class="px-4 py-2">
                <div>{{ u.start_date || '—' }} → {{ u.end_date || '—' }}</div>
              </td>
              <td class="px-4 py-2">
                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs border">
                  {{ u.status || '—' }}
                </span>
              </td>
              <td class="px-4 py-2">
                <div class="flex items-center gap-2 flex-nowrap whitespace-nowrap">
                  <a :href="detailsPath(u.id)" target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center justify-center h-9 px-3 text-xs font-medium border rounded-md hover:bg-gray-50">
                    Details
                  </a>

                  <button v-if="can('units.update')"
                    class="inline-flex items-center justify-center h-9 px-3 text-xs font-medium border rounded-md hover:bg-gray-50"
                    @click="openEdit(u)">
                    Edit
                  </button>

                  <button v-if="can('units.delete')"
                    class="inline-flex items-center justify-center h-9 px-3 text-xs font-medium border rounded-md text-red-600 hover:bg-red-50"
                    @click="remove(u)">
                    Delete
                  </button>
                </div>
              </td>

            </tr>

            <tr v-if="!loading && !filtered.length">
              <td colspan="6" class="px-4 py-8 text-center text-gray-500">No Projects found.</td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-4 py-3 border-t">
          <div class="text-xs text-gray-500">
            Showing
            <strong>{{ startIndex + 1 }}</strong>–<strong>{{ endIndex }}</strong>
            of <strong>{{ filtered.length }}</strong>
          </div>
          <div class="flex items-center gap-2">
            <button class="px-3 py-1.5 border rounded disabled:opacity-50" :disabled="page === 1" @click="page--">
              Prev
            </button>
            <span class="text-sm">Page {{ page }}</span>
            <button class="px-3 py-1.5 border rounded disabled:opacity-50"
              :disabled="page * pageSize >= filtered.length" @click="page++">
              Next
            </button>
            <select v-model.number="pageSize" class="form-input w-24">
              <option :value="10">10</option>
              <option :value="20">20</option>
              <option :value="50">50</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Details Modal -->
      <div v-if="showDetails" class="fixed inset-0 z-50 modal-top">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeDetails"></div>

        <div class="relative mx-auto w-full max-w-5xl p-6 sm:p-8 h-full flex items-center justify-center">
          <div class="bg-white rounded-2xl shadow-2xl w-full max-h-[90vh] overflow-y-auto transition-all">
            <!-- Header -->
            <div
              class="sticky top-0 bg-gradient-to-r from-yellow-50 to-yellow-100 border-b px-6 py-4 rounded-t-2xl flex items-center justify-between">
              <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                Project Details
              </h3>
              <button @click="closeDetails"
                class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-full p-2">
                ✕
              </button>
            </div>

            <!-- Loading -->
            <div v-if="details.loading" class="p-5 text-sm text-gray-500">Loading…</div>

            <!-- Content -->
            <div v-else class="p-6 space-y-6">
              <!-- Cover Section -->
              <div class="relative rounded-xl overflow-hidden shadow-md h-64 sm:h-72 md:h-80">
                <img v-if="d.cover_image_url" :src="d.cover_image_url" alt="Project Cover"
                  class="absolute inset-0 w-full h-full object-cover" />
                <div v-else class="absolute inset-0 flex items-center justify-center bg-gray-100 text-gray-400 text-sm">
                  No Cover Image
                </div>

                <!-- Overlay text -->
                <div
                  class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex flex-col justify-end p-6 text-white">
                  <div class="text-2xl font-bold">
                    {{ d.name_en || 'Unnamed Project' }}
                  </div>
                  <div v-if="d.name_ar" class="text-sm text-gray-200" dir="rtl">
                    {{ d.name_ar }}
                  </div>
                </div>
              </div>

              <!-- Quick Info -->
              <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                  <span class="block text-gray-400 text-xs uppercase">Status</span>
                  <span class="font-semibold text-gray-800">{{ d.status || '—' }}</span>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                  <span class="block text-gray-400 text-xs uppercase">Location</span>
                  <span class="font-semibold text-gray-800">{{ d.location || '—' }}</span>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                  <span class="block text-gray-400 text-xs uppercase">Duration</span>
                  <span class="font-semibold text-gray-800">
                    {{ d.start_date || '—' }} → {{ d.end_date || '—' }}
                  </span>
                </div>
              </div>

              <!-- Description -->
              <div class="bg-white border rounded-xl p-5 shadow-sm">
                <div class="mb-2 text-gray-700 font-semibold text-base">Description (EN)</div>
                <p class="text-sm text-gray-700 whitespace-pre-wrap mb-4">
                  {{ d.description_en || '—' }}
                </p>

                <div class="mb-2 text-gray-700 font-semibold text-base">Description (AR)</div>
                <p class="text-sm text-gray-700 whitespace-pre-wrap" dir="rtl">
                  {{ d.description_ar || '—' }}
                </p>
              </div>

              <!-- Gallery -->
              <div>
                <div class="text-gray-800 font-semibold mb-3">Gallery</div>
                <div v-if="d.gallery && d.gallery.length" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                  <div v-for="g in d.gallery" :key="g.id"
                    class="aspect-[4/3] rounded-lg overflow-hidden bg-gray-100 border hover:shadow-md transition">
                    <img :src="g.image_url" class="w-full h-full object-cover" />
                  </div>
                </div>
                <div v-else class="text-sm text-gray-500">No images available.</div>
              </div>

              <!-- Navigation Buttons -->
              <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 pt-4 border-t">
                <Link :href="docsPath(d.id)" class="nav-btn">
                  📄 Docs
                </Link>
                <Link :href="galleryPath(d.id)" class="nav-btn">
                  🖼️ Gallery
                </Link>
                <Link :href="drawingPath(d.id)" class="nav-btn">
                  ✏️ Drawings
                </Link>
                <Link :href="reportsPath(d.id)" class="nav-btn">
                  📊 Reports
                </Link>
                <Link :href="phasesPath(d.id)" class="nav-btn">
                  🧩 Phases
                </Link>
                <Link :href="timelinePath(d.id)" class="nav-btn">
                  🕒 Timeline
                </Link>
                <Link :href="unitContractorsPath(d.id)" class="nav-btn">
                  👷 Assignments
                </Link>
                <Link :href="unitPaymentsPath(d.id)" class="nav-btn">
                  💰 Project Value & Payments
                </Link>
              </div>

              <!-- Footer -->
              <div class="sticky bottom-0 bg-white border-t pt-3 text-right">
                <button @click="closeDetails" class="px-4 py-2 border rounded-lg hover:bg-gray-100 text-gray-700">
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Owner Create Modal -->
      <div v-if="showOwnerModal" class="fixed inset-0 z-[60]" @keydown.esc="closeOwnerCreate">
        <div class="absolute inset-0 bg-black/50" @click="closeOwnerCreate" aria-hidden="true"></div>

        <div class="relative mx-auto w-full max-w-xl p-4 sm:p-6 h-full flex items-center justify-center">
          <div class="bg-white rounded-2xl shadow-lg w-full max-h-[min(88vh,800px)] overflow-y-auto focus:outline-none">
            <div class="sticky top-0 z-10 bg-white border-b rounded-t-2xl">
              <div class="flex items-center justify-between px-5 py-3">
                <h3 class="text-lg font-bold">Add Owner</h3>
                <button @click="closeOwnerCreate" class="text-gray-400 hover:text-gray-600" aria-label="Close">✕</button>
              </div>
            </div>

            <div class="px-5 py-4 space-y-4">
              <form @submit.prevent="submitOwnerCreate" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Name*</label>
                    <input :class="ownerInputClass('name')" v-model="ownerForm.name" type="text" />
                    <p v-if="ownerErr('name')" class="mt-1 text-xs text-red-600">{{ ownerErr('name') }}</p>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Email*</label>
                    <input :class="ownerInputClass('email')" v-model="ownerForm.email" type="email" />
                    <p v-if="ownerErr('email')" class="mt-1 text-xs text-red-600">{{ ownerErr('email') }}</p>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Phone*</label>
                    <input :class="ownerInputClass('phone')" v-model="ownerForm.phone" type="text" />
                    <p v-if="ownerErr('phone')" class="mt-1 text-xs text-red-600">{{ ownerErr('phone') }}</p>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Password*</label>
                    <input :class="ownerInputClass('password')" v-model="ownerForm.password" type="password" />
                    <p v-if="ownerErr('password')" class="mt-1 text-xs text-red-600">{{ ownerErr('password') }}</p>
                  </div>

                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Country Code*</label>
                    <select :class="ownerInputClass('country_code')" v-model="ownerForm.country_code">
                      <option value="+20">+20</option>
                      <option value="+965">+965</option>
                    </select>
                    <p v-if="ownerErr('country_code')" class="mt-1 text-xs text-red-600">{{ ownerErr('country_code') }}</p>
                  </div>

                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Country Name*</label>
                    <select :class="ownerInputClass('country_name')" v-model="ownerForm.country_name">
                      <option value="Egypt">Egypt</option>
                      <option value="Kuwait">Kuwait</option>
                    </select>
                    <p v-if="ownerErr('country_name')" class="mt-1 text-xs text-red-600">{{ ownerErr('country_name') }}</p>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Gender*</label>
                    <select :class="ownerInputClass('gender')" v-model="ownerForm.gender">
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                    </select>
                    <p v-if="ownerErr('gender')" class="mt-1 text-xs text-red-600">{{ ownerErr('gender') }}</p>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Profile Image*</label>
                    <input :class="ownerInputClass('profile_image')" type="file" accept="image/*" @change="onOwnerImage" />
                    <p v-if="ownerErr('profile_image')" class="mt-1 text-xs text-red-600">{{ ownerErr('profile_image') }}</p>

                    <div v-if="ownerImagePreview" class="mt-2 inline-block">
                      <img :src="ownerImagePreview" class="w-24 h-24 object-cover rounded-full border" />
                    </div>
                  </div>
                </div>

                <div v-if="ownerErrorMsg" class="rounded border border-red-200 bg-red-50 text-red-700 px-3 py-2">
                  {{ ownerErrorMsg }}
                </div>
              </form>
            </div>

            <div class="sticky bottom-0 z-10 bg-white border-t rounded-b-2xl">
              <div class="px-5 py-3 flex items-center gap-3">
                <button :disabled="savingOwner" class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-60"
                  @click="submitOwnerCreate">
                  {{ savingOwner ? 'Saving…' : 'Create' }}
                </button>
                <button type="button" class="px-3 py-2 border rounded" @click="closeOwnerCreate">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Project Manager Create Modal -->
      <div v-if="showPmModal" class="fixed inset-0 z-[60]" @keydown.esc="closePmCreate">
        <div class="absolute inset-0 bg-black/50" @click="closePmCreate" aria-hidden="true"></div>

        <div class="relative mx-auto w-full max-w-xl p-4 sm:p-6 h-full flex items-center justify-center">
          <div class="bg-white rounded-2xl shadow-lg w-full max-h-[min(88vh,800px)] overflow-y-auto focus:outline-none">
            <div class="sticky top-0 z-10 bg-white border-b rounded-t-2xl">
              <div class="flex items-center justify-between px-5 py-3">
                <h3 class="text-lg font-bold">Add Project Manager</h3>
                <button @click="closePmCreate" class="text-gray-400 hover:text-gray-600" aria-label="Close">✕</button>
              </div>
            </div>

            <div class="px-5 py-4 space-y-4">
              <form @submit.prevent="submitPmCreate" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Name*</label>
                    <input :class="pmInputClass('name')" v-model="pmForm.name" type="text" />
                    <p v-if="pmErr('name')" class="mt-1 text-xs text-red-600">{{ pmErr('name') }}</p>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Email*</label>
                    <input :class="pmInputClass('email')" v-model="pmForm.email" type="email" />
                    <p v-if="pmErr('email')" class="mt-1 text-xs text-red-600">{{ pmErr('email') }}</p>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Phone*</label>
                    <input :class="pmInputClass('phone')" v-model="pmForm.phone" type="text" />
                    <p v-if="pmErr('phone')" class="mt-1 text-xs text-red-600">{{ pmErr('phone') }}</p>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Password*</label>
                    <input :class="pmInputClass('password')" v-model="pmForm.password" type="password" />
                    <p v-if="pmErr('password')" class="mt-1 text-xs text-red-600">{{ pmErr('password') }}</p>
                  </div>

                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Country Code*</label>
                    <select :class="pmInputClass('country_code')" v-model="pmForm.country_code">
                      <option value="+20">+20</option>
                      <option value="+965">+965</option>
                    </select>
                    <p v-if="pmErr('country_code')" class="mt-1 text-xs text-red-600">{{ pmErr('country_code') }}</p>
                  </div>

                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Country Name*</label>
                    <select :class="pmInputClass('country_name')" v-model="pmForm.country_name">
                      <option value="Egypt">Egypt</option>
                      <option value="Kuwait">Kuwait</option>
                    </select>
                    <p v-if="pmErr('country_name')" class="mt-1 text-xs text-red-600">{{ pmErr('country_name') }}</p>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Gender*</label>
                    <select :class="pmInputClass('gender')" v-model="pmForm.gender">
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                    </select>
                    <p v-if="pmErr('gender')" class="mt-1 text-xs text-red-600">{{ pmErr('gender') }}</p>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Profile Image*</label>
                    <input :class="pmInputClass('profile_image')" type="file" accept="image/*" @change="onPmImage" />
                    <p v-if="pmErr('profile_image')" class="mt-1 text-xs text-red-600">{{ pmErr('profile_image') }}</p>

                    <div v-if="pmImagePreview" class="mt-2 inline-block">
                      <img :src="pmImagePreview" class="w-24 h-24 object-cover rounded-full border" />
                    </div>
                  </div>
                </div>

                <div v-if="pmErrorMsg" class="rounded border border-red-200 bg-red-50 text-red-700 px-3 py-2">
                  {{ pmErrorMsg }}
                </div>
              </form>
            </div>

            <div class="sticky bottom-0 z-10 bg-white border-t rounded-b-2xl">
              <div class="px-5 py-3 flex items-center gap-3">
                <button :disabled="savingPm" class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-60"
                  @click="submitPmCreate">
                  {{ savingPm ? 'Saving…' : 'Create' }}
                </button>
                <button type="button" class="px-3 py-2 border rounded" @click="closePmCreate">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Add/Edit Modal (restored) -->
      <div v-if="showModal" class="fixed inset-0 z-50" @keydown.esc="closeModal">
        <div class="absolute inset-0 bg-black/40 back-drop" @click="closeModal" aria-hidden="true"></div>

        <div class="relative mx-auto w-full max-w-3xl p-4 sm:p-6 h-full flex items-center justify-center">
          <div class="bg-white rounded-2xl shadow-lg w-full max-h-[min(88vh,900px)] overflow-y-auto focus:outline-none">
            <!-- Header -->
            <div class="sticky top-0 z-10 bg-white border-b rounded-t-2xl">
              <div class="flex items-center justify-between px-5 py-3">
                <h3 class="text-lg font-bold">{{ editingId ? 'Edit Project' : 'Add Project' }}</h3>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600" aria-label="Close">✕</button>
              </div>
            </div>

            <!-- Body -->
            <div class="px-5 py-4 space-y-4">
              <form @submit.prevent="submit" class="space-y-4">
                <!-- names -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Name (EN)*</label>
                    <input :class="inputClass('name.en')" v-model="form.name.en" type="text" :required="!editingId" />
                    <p v-if="err('name.en')" class="mt-1 text-xs text-red-600">{{ err('name.en') }}</p>
                  </div>
                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Name (AR)*</label>
                    <input :class="inputClass('name.ar')" v-model="form.name.ar" type="text" :required="!editingId" />
                    <p v-if="err('name.ar')" class="mt-1 text-xs text-red-600">{{ err('name.ar') }}</p>
                  </div>
                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Description (EN)*</label>
                    <textarea :class="inputClass('description.en')" v-model="form.description.en"
                      :required="!editingId" />
                    <p v-if="err('description.en')" class="mt-1 text-xs text-red-600">{{ err('description.en') }}</p>
                  </div>
                  <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Description (AR)*</label>
                    <textarea :class="inputClass('description.ar')" v-model="form.description.ar"
                      :required="!editingId" />
                    <p v-if="err('description.ar')" class="mt-1 text-xs text-red-600">{{ err('description.ar') }}</p>
                  </div>
                </div>

                <!-- map + coords -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="md:col-span-2">
                    <MapPicker label="Pick on map" v-model:lat="form.lat" v-model:lng="form.long"
                      v-model:address="form.location" />
                  </div>

                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Location*</label>
                    <input :class="inputClass('location')" v-model="form.location" type="text" :required="!editingId"
                      disabled />
                    <p v-if="err('location')" class="mt-1 text-xs text-red-600">{{ err('location') }}</p>
                  </div>                  
                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Address(optional)</label>
                    <input :class="inputClass('address')" v-model="form.address" type="text" :required="!editingId"
                       />
                    <p v-if="err('address')" class="mt-1 text-xs text-red-600">{{ err('address') }}</p>
                  </div>
                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Latitude (-90..90)*</label>
                    <input :class="inputClass('lat')" v-model.number="form.lat" type="number" step="any" min="-90"
                      max="90" :required="!editingId" disabled />
                    <p v-if="err('lat')" class="mt-1 text-xs text-red-600">{{ err('lat') }}</p>
                  </div>
                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Longitude (-180..180)*</label>
                    <input :class="inputClass('long')" v-model.number="form.long" type="number" step="any" min="-180"
                      max="180" :required="!editingId" disabled />
                    <p v-if="err('long')" class="mt-1 text-xs text-red-600">{{ err('long') }}</p>
                  </div>

                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Start Date*</label>
                    <input :class="inputClass('start_date')" v-model="form.start_date" type="datetime-local"
                      :required="!editingId" />
                    <p v-if="err('start_date')" class="mt-1 text-xs text-red-600">{{ err('start_date') }}</p>
                  </div>
                  <div>
                    <label class="block text-xs text-gray-500 mb-1">End Date*</label>
                    <input :class="inputClass('end_date')" v-model="form.end_date" type="datetime-local"
                      :required="!editingId" />
                    <p v-if="err('end_date')" class="mt-1 text-xs text-red-600">{{ err('end_date') }}</p>
                  </div>
                  <!-- Extension Dates -->
                  <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                      <label class="block text-xs text-gray-500">
                        Extension Dates (optional)
                      </label>

                      <button type="button" class="text-xs px-2 py-1 border rounded hover:bg-gray-50"
                        @click="form.extension_dates.push('')">
                        + Add extension
                      </button>
                    </div>

                    <div v-if="form.extension_dates?.length > 0" class="space-y-2">
                      <div v-for="(ex, i) in form.extension_dates" :key="i" class="flex items-center gap-2">
                        <input class="form-input" type="datetime-local" v-model="form.extension_dates[i]"
                          placeholder="YYYY-MM-DDTHH:mm" />

                        <button type="button" class="px-2 py-2 border rounded text-red-600 hover:bg-red-50"
                          @click="form.extension_dates.splice(i, 1)" title="Remove">
                          ✕
                        </button>
                      </div>
                    </div>

                    <p v-if="err('extension_dates')" class="mt-1 text-xs text-red-600">
                      {{ err('extension_dates') }}
                    </p>
                  </div>

                </div>

                <!-- associations + status -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Owner *</label>
                    <div class="flex items-center gap-2">
                      <select :class="inputClass('user_id')" v-model.number="form.user_id" :required="!editingId">
                        <option value="" disabled>Select an Owner</option>
                        <option v-for="u in userOptions" :key="u.id" :value="u.id">
                          {{ u.name }} — {{ u.email }}
                        </option>
                      </select>
                      <button type="button"
                        class="shrink-0 h-9 px-3 text-sm border rounded-md bg-black text-white hover:bg-gray-800 whitespace-nowrap"
                        title="Add new Owner" @click="openOwnerCreate">
                        Add Owner
                      </button>
                    </div>
                    <p v-if="err('user_id')" class="mt-1 text-xs text-red-600">{{ err('user_id') }}</p>
                  </div>

                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Project Manager *</label>
                    <div class="flex items-center gap-2">
                      <select :class="inputClass('vendor_id')" v-model.number="form.vendor_id" :required="!editingId">
                        <option value="" disabled>Select a Project Manager</option>
                        <option v-for="v in vendorOptions" :key="v.id" :value="v.id">
                          {{ v.name }} — {{ v.email }}
                        </option>
                      </select>
                      <button type="button"
                        class="shrink-0 h-9 px-3 text-sm border rounded-md bg-black text-white hover:bg-gray-800 whitespace-nowrap"
                        title="Add new Project Manager" @click="openPmCreate">
                        Add PM
                      </button>
                    </div>
                    <p v-if="err('vendor_id')" class="mt-1 text-xs text-red-600">{{ err('vendor_id') }}</p>
                  </div>

                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Status</label>
                    <select class="form-input" v-model="form.status">
                      <option value="under_construction" selected>under_construction</option>
                      <option value="completed">completed</option>
                    </select>
                  </div>
                </div>

                <!-- files -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs text-gray-500 mb-1">Gallery* (multiple on create)</label>
                    <input :class="inputClass('gallery')" type="file" accept="image/*" multiple @change="onGallery"
                      :required="!editingId" />
                    <p v-if="err('gallery')" class="mt-1 text-xs text-red-600">{{ err('gallery') }}</p>

                    <!-- Existing gallery -->
                    <div v-if="existingGallery.length" class="mt-2 flex flex-wrap gap-2">
                      <div v-for="g in existingGallery" :key="g.id" class="relative inline-block">
                        <img :src="g.image_url" class="w-24 h-16 object-cover rounded border" />
                        <button type="button"
                          class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center shadow hover:bg-red-600"
                          @click="removeGalleryImage(g.id)">
                          ✕
                        </button>
                      </div>
                    </div>

                    <!-- New uploads -->
                    <div v-if="galleryPreviews.length" class="mt-2 flex flex-wrap gap-2">
                      <div v-for="(src, i) in galleryPreviews" :key="i" class="relative inline-block">
                        <img :src="src" class="w-24 h-16 object-cover rounded border" />
                        <button type="button"
                          class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center shadow hover:bg-red-600"
                          @click="galleryPreviews.splice(i, 1)">
                          ✕
                        </button>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs text-gray-500 mb-1">
                      Cover Image* {{ editingId ? '(optional to replace)' : '' }}
                    </label>
                    <input :class="inputClass('cover_image')" type="file" accept="image/*" @change="onCover"
                      :required="!editingId" />
                    <p v-if="err('cover_image')" class="mt-1 text-xs text-red-600">{{ err('cover_image') }}</p>

                    <div v-if="coverPreview" class="mt-2 relative inline-block">
                      <img :src="coverPreview" class="w-40 h-28 object-cover rounded border" />
                      <button type="button"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow hover:bg-red-600"
                        @click="removeCover">
                        ✕
                      </button>
                    </div>
                  </div>

                </div>

                <!-- top alert -->
                <div v-if="errorMsg" class="rounded border border-red-200 bg-red-50 text-red-700 px-3 py-2">
                  {{ errorMsg }}
                </div>
              </form>
            </div>

            <!-- Footer -->
            <div class="sticky bottom-0 z-10 bg-white border-t rounded-b-2xl">
              <div class="px-5 py-3 flex items-center gap-3">
                <button :disabled="saving" class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-60"
                  @click="submit">
                  {{ saving ? 'Saving…' : (editingId ? 'Update' : 'Create') }}
                </button>
                <button type="button" class="px-3 py-2 border rounded" @click="closeModal">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UnitNav from '@/Components/UnitNav.vue'
import MapPicker from '@/Components/MapPicker.vue'
import { ref, onMounted, computed, reactive, nextTick, watch } from 'vue'
import { UnitsApi, buildUnitCreateFD, buildUnitUpdateFD } from '@/Services/units'
import { Link, usePage } from '@inertiajs/vue3'
import { UsersApi, buildUserCreateFD } from '@/Services/users'
import { VendorsApi, buildVendorCreateFD } from '@/Services/vendors'
import { useServerError } from '@/composables/useServerError'

const inertiaPage = usePage()
const role = computed(() => inertiaPage.props.auth?.role)
const userPermissions = computed(() => inertiaPage.props.auth?.permissions ?? [])
function can(permission) {
  if (role.value === 'admin') return true
  return userPermissions.value.includes(permission)
}

const { show } = useServerError()

/* ------------------ table state ------------------ */
const rows = ref([])
const loading = ref(false)

const filters = reactive({ q: '', status: '', from: '', to: '' })
const page = ref(1)
const pageSize = ref(10)

const filtered = computed(() => {
  let r = [...rows.value]
  const q = filters.q.trim().toLowerCase()
  if (q) {
    r = r.filter(x =>
      (x.name_en || '').toLowerCase().includes(q) ||
      (x.name_ar || '').toLowerCase().includes(q) ||
      (x.location || '').toLowerCase().includes(q)
    )
  }
  if (filters.status) r = r.filter(x => x.status === filters.status)
  if (filters.from) r = r.filter(x => !x.start_date || x.start_date >= filters.from)
  if (filters.to) r = r.filter(x => !x.end_date || x.end_date <= filters.to)

  // order sorting

  r.sort((a, b) => {
    const da = Date.parse(a.created_at || a.createdAt || '')
    const db = Date.parse(b.created_at || b.createdAt || '')
    if (Number.isNaN(da) || Number.isNaN(db)) return (b.id || 0) - (a.id || 0)
    return db - da
  })
  return r
})

const startIndex = computed(() => (page.value - 1) * pageSize.value)
const endIndex = computed(() => Math.min(startIndex.value + pageSize.value, filtered.value.length))
const paginated = computed(() => filtered.value.slice(startIndex.value, endIndex.value))

watch([() => filters.q, () => filters.status, () => filters.from, () => filters.to, pageSize], () => {
  page.value = 1
})

async function fetchUnits() {
  loading.value = true
  try {
    rows.value = await UnitsApi.list()
  } finally {
    loading.value = false
  }
}

/* ------------------ details modal ------------------ */
/* ------------------ details modal ------------------ */
const showDetails = ref(false)
const details = reactive({
  loading: false,
  data: null,
})

// ✅ FIXED: make it reactive
const d = computed(() => details.data || {})

async function openDetails(u) {
  showDetails.value = true
  details.loading = true
  try {
    const data = await UnitsApi.show(u.id)
    details.data = data
  } catch (e) {
    console.error(e)
    details.data = u
  } finally {
    details.loading = false
  }
}

function closeDetails() {
  showDetails.value = false
  details.data = null
}

/* ------------------ create/edit modal (restored) ------------------ */
const showModal = ref(false)
const editingId = ref(null)

const errors = ref({})
const errorMsg = ref('')
const saving = ref(false)

const form = ref({
  name: { en: '', ar: '' },
  description: { en: '', ar: '' },
  location: '',
  address: '',
  lat: null,
  long: null,
  start_date: '',
  end_date: '',
  extension_dates: [],
  user_id: null,
  vendor_id: null,
  status: 'under_construction',
  cover_image: null,
  gallery: [],
})

const coverPreview = ref(null)
const galleryPreviews = ref([])
const existingGallery = ref([])

const userOptions = ref([])
const vendorOptions = ref([])

const setFieldError = (field, msgs) => {
  if (!msgs || (Array.isArray(msgs) && msgs.length === 0)) {
    delete errors.value[field]; return
  }
  errors.value[field] = Array.isArray(msgs) ? msgs : [String(msgs)]
}
const err = (field) => errors.value[field]?.[0] || ''
const hasErr = (field) => Boolean(errors.value[field])
const inputClass = (field) => ['form-input', hasErr(field) ? 'border-red-400 focus:ring-red-500' : ''].join(' ')


function docsPath(id) {
  return `/projects-management/${id}/docs`
}
function galleryPath(id) {
  return `/projects-management/${id}/gallery`
}
function drawingPath(id) {
  return `/projects-management/${id}/drawing`
}
function reportsPath(id) {
  return `/projects-management/${id}/reports`
}
function phasesPath(id) {
  return `/projects-management/${id}/phases`
}
function timelinePath(id) {
  return `/projects-management/${id}/timeline`
}
function unitContractorsPath(id) {
  return `/projects-management/${id}/contractors`
}
function unitPaymentsPath(id) {
  return `/projects-management/${id}/payments`
}

function detailsPath(id) {
  return `/projects-management/${id}/details`
}

function toDatetimeLocal(v) {
  if (!v) return ''
  const s = String(v).replace(' ', 'T')
  return s.slice(0, 16)
}


function scrollToFirstError() {
  const modalBody = document.querySelector('.max-h-\\[min\\(88vh\\,900px\\)\\]')
  const first = modalBody?.querySelector('input.border-red-400, select.border-red-400, textarea.border-red-400')
  if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' })
}

function validate() {
  errors.value = {}
  errorMsg.value = ''
  const f = form.value
  const need = (v) => v !== null && v !== undefined && String(v).trim() !== ''

  if (!editingId.value) {
    if (!need(f.name.en)) setFieldError('name.en', 'Name (EN) is required')
    if (!need(f.name.ar)) setFieldError('name.ar', 'Name (AR) is required')
    if (!need(f.description.en)) setFieldError('description.en', 'Description (EN) is required')
    if (!need(f.description.ar)) setFieldError('description.ar', 'Description (AR) is required')
    if (!need(f.location)) setFieldError('location', 'Location is required')
    if (!(typeof f.lat === 'number' && f.lat >= -90 && f.lat <= 90)) setFieldError('lat', 'Latitude must be between -90 and 90')
    if (!(typeof f.long === 'number' && f.long >= -180 && f.long <= 180)) setFieldError('long', 'Longitude must be between -180 and 180')
    if (!need(f.start_date)) setFieldError('start_date', 'Start date is required')
    if (!need(f.end_date)) setFieldError('end_date', 'End date is required')
    if (need(f.start_date) && need(f.end_date) && new Date(f.end_date) < new Date(f.start_date)) setFieldError('end_date', 'End date must be after or equal to start date')
    if (!need(f.user_id)) setFieldError('user_id', 'Owner is required')
    if (!need(f.vendor_id)) setFieldError('vendor_id', 'Project Manager is required')
    if (!f.cover_image) setFieldError('cover_image', 'Cover image is required')
    if (!Array.isArray(f.gallery) || f.gallery.length === 0) setFieldError('gallery', 'Add at least one gallery image')
  } else {
    if (f.lat !== null && (f.lat < -90 || f.lat > 90)) setFieldError('lat', 'Latitude must be between -90 and 90')
    if (f.long !== null && (f.long < -180 || f.long > 180)) setFieldError('long', 'Longitude must be between -180 and 180')
    if (need(f.start_date) && need(f.end_date) && new Date(f.end_date) < new Date(f.start_date)) setFieldError('end_date', 'End date must be after or equal to start date')
  }

  if (Object.keys(errors.value).length) {
    errorMsg.value = 'Please fix the highlighted fields.'
    return false
  }
  return true
}

function openCreate() {
  resetForm()
  showModal.value = true
}
async function openEdit(u) {
  resetForm()
  editingId.value = u.id
  const data = await UnitsApi.show(u.id)

  form.value.name.en = data.name_en || ''
  form.value.name.ar = data.name_ar || ''
  form.value.description.en = data.description_en || ''
  form.value.description.ar = data.description_ar || ''
  form.value.location = data.location || ''
  form.value.address = data.address || ''
  form.value.lat = data.lat
  form.value.long = data.long
  form.value.start_date = toDatetimeLocal(data.start_date)
  form.value.end_date = toDatetimeLocal(data.end_date)
  form.value.extension_dates = Array.isArray(data.extension_dates) ? data.extension_dates.map(toDatetimeLocal) : []
  form.value.user_id = data.user_id ?? null
  form.value.vendor_id = data.vendor_id ?? null
  form.value.status = data.status || 'under_construction'

  coverPreview.value = data.cover_image_url || null
  existingGallery.value = Array.isArray(data.gallery) ? data.gallery : []
  showModal.value = true
}

function resetForm() {
  form.value = {
    name: { en: '', ar: '' },
    description: { en: '', ar: '' },
    location: '',
    address: '',
    lat: null,
    long: null,
    start_date: '',
    end_date: '',
    extension_dates: [],
    user_id: null,
    vendor_id: null,
    status: 'under_construction',
    cover_image: null,
    gallery: [],
  }
  coverPreview.value = null
  galleryPreviews.value = []
  existingGallery.value = []
  errors.value = {}
  errorMsg.value = ''
  editingId.value = null
}

function closeModal() { showModal.value = false }

function onCover(e) {
  const f = e.target.files?.[0] || null
  form.value.cover_image = f
  coverPreview.value = f ? URL.createObjectURL(f) : coverPreview.value
}
function onGallery(e) {
  const picked = Array.from(e.target.files || [])
  if (!picked.length) return

  form.value.gallery = [...form.value.gallery, ...picked]

  galleryPreviews.value = [
    ...galleryPreviews.value,
    ...picked.map(f => URL.createObjectURL(f)),
  ]

  e.target.value = null
}


async function removeCover() {
  if (!confirm('Delete cover image?')) return
  await UnitsApi.deleteCover(editingId.value)
  coverPreview.value = null
}
async function removeGalleryImage(imageId) {
  if (!confirm('Delete this image?')) return
  await UnitsApi.deleteGalleryImage(editingId.value, imageId)
  existingGallery.value = existingGallery.value.filter(g => g.id !== imageId)
}

function applyServerErrors(e) {
  const errs = e?.response?.data?.errors
  if (!errs) return false
  errors.value = {}
  for (const [k, arr] of Object.entries(errs)) setFieldError(k, arr)
  errorMsg.value = e?.response?.data?.message || 'Validation failed. Please review the fields below.'
  return true
}

async function submit() {
  saving.value = true
  try {
    if (!validate()) {
      await nextTick()
      scrollToFirstError()
      return
    }
    if (editingId.value) {
      const fd = buildUnitUpdateFD(form.value)
      await UnitsApi.update(editingId.value, fd)
    } else {
      const fd = buildUnitCreateFD(form.value)
      await UnitsApi.create(fd)
    }
    await fetchUnits()
    closeModal()
  } catch (e) {
    show(e)
    const status = e?.response?.status
    if (status === 422 && applyServerErrors(e)) {
      await nextTick()
      scrollToFirstError()
    } else {
      errorMsg.value = e?.response?.data?.message || e?.message || 'Request failed'
    }
  } finally {
    saving.value = false
  }
}

async function remove(u) {
  if (!confirm(`Delete project #${u.id}?`)) return
  await UnitsApi.remove(u.id)
  await fetchUnits()
}

/* ------------------ options ------------------ */
async function fetchOptions() {
  const users = await UsersApi.list()
  userOptions.value = users.map(u => ({ id: u.id, name: u.name, email: u.email }))
  const vendors = await VendorsApi.list()
  vendorOptions.value = vendors.map(v => ({ id: v.id, name: v.name, email: v.email }))
}

/* ------------------ owner-create modal ------------------ */
const showOwnerModal = ref(false)
const savingOwner = ref(false)
const ownerErrors = ref({})
const ownerErrorMsg = ref('')
const ownerImageFile = ref(null)
const ownerImagePreview = ref(null)

const ownerForm = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  country_code: '+20',
  country_name: 'Egypt',
  gender: 'male',
  profile_image: null,
})

const ownerErr = (field) => ownerErrors.value[field]?.[0] || ''
const ownerHasErr = (field) => Boolean(ownerErrors.value[field])
const ownerInputClass = (field) =>
  ['form-input', ownerHasErr(field) ? 'border-red-400 focus:ring-red-500' : ''].join(' ')

function resetOwnerForm() {
  ownerForm.value = {
    name: '',
    email: '',
    phone: '',
    password: '',
    country_code: '+20',
    country_name: 'Egypt',
    gender: 'male',
    profile_image: null,
  }
  ownerImageFile.value = null
  ownerImagePreview.value = null
  ownerErrors.value = {}
  ownerErrorMsg.value = ''
}

function openOwnerCreate() {
  resetOwnerForm()
  showOwnerModal.value = true
}

function closeOwnerCreate() {
  showOwnerModal.value = false
}

function onOwnerImage(e) {
  const f = e.target.files?.[0] || null
  ownerImageFile.value = f
  ownerForm.value.profile_image = f
  ownerImagePreview.value = f ? URL.createObjectURL(f) : null
}

function setOwnerFieldError(field, msgs) {
  if (!msgs || (Array.isArray(msgs) && msgs.length === 0)) {
    delete ownerErrors.value[field]; return
  }
  ownerErrors.value[field] = Array.isArray(msgs) ? msgs : [String(msgs)]
}

function validateOwner() {
  ownerErrors.value = {}
  ownerErrorMsg.value = ''
  const f = ownerForm.value
  const need = (v) => v !== null && v !== undefined && String(v).trim() !== ''

  if (!need(f.name)) setOwnerFieldError('name', 'Name is required')
  if (!need(f.email)) setOwnerFieldError('email', 'Email is required')
  else if (!/^\S+@\S+\.\S+$/.test(f.email)) setOwnerFieldError('email', 'Invalid email')
  if (!need(f.phone)) setOwnerFieldError('phone', 'Phone is required')
  if (!need(f.password)) setOwnerFieldError('password', 'Password is required')
  else if (f.password.length < 6) setOwnerFieldError('password', 'Minimum 6 characters')
  else if (!/[a-zA-Z]/.test(f.password)) setOwnerFieldError('password', 'Must include letters')
  else if (!/\d/.test(f.password)) setOwnerFieldError('password', 'Must include numbers')
  if (!need(f.country_code)) setOwnerFieldError('country_code', 'Country code is required')
  if (!need(f.country_name)) setOwnerFieldError('country_name', 'Country name is required')
  if (!need(f.gender)) setOwnerFieldError('gender', 'Gender is required')
  if (!f.profile_image) setOwnerFieldError('profile_image', 'Image is required')

  if (Object.keys(ownerErrors.value).length) {
    ownerErrorMsg.value = 'Please fix the highlighted fields.'
    return false
  }
  return true
}

async function submitOwnerCreate() {
  if (!validateOwner()) return
  savingOwner.value = true
  try {
    const fd = buildUserCreateFD({ ...ownerForm.value, profile_image: ownerImageFile.value })
    await UsersApi.create(fd)

    const emailJustCreated = ownerForm.value.email
    const users = await UsersApi.list()
    userOptions.value = users.map(u => ({ id: u.id, name: u.name, email: u.email }))

    const justCreated = userOptions.value.find(u => u.email === emailJustCreated)
    if (justCreated) form.value.user_id = justCreated.id

    closeOwnerCreate()
  } catch (e) {
    show(e)
    const status = e?.response?.status
    const errs = e?.response?.data?.errors
    if (status === 422 && errs) {
      for (const [k, arr] of Object.entries(errs)) setOwnerFieldError(k, arr)
      ownerErrorMsg.value = e?.response?.data?.message || 'Validation failed.'
    } else {
      ownerErrorMsg.value = e?.response?.data?.message || e?.message || 'Request failed'
    }
  } finally {
    savingOwner.value = false
  }
}

/* ------------------ project-manager-create modal ------------------ */
const showPmModal = ref(false)
const savingPm = ref(false)
const pmErrors = ref({})
const pmErrorMsg = ref('')
const pmImageFile = ref(null)
const pmImagePreview = ref(null)

const pmForm = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  country_code: '+20',
  country_name: 'Egypt',
  gender: 'male',
  profile_image: null,
})

const pmErr = (field) => pmErrors.value[field]?.[0] || ''
const pmHasErr = (field) => Boolean(pmErrors.value[field])
const pmInputClass = (field) =>
  ['form-input', pmHasErr(field) ? 'border-red-400 focus:ring-red-500' : ''].join(' ')

function resetPmForm() {
  pmForm.value = {
    name: '',
    email: '',
    phone: '',
    password: '',
    country_code: '+20',
    country_name: 'Egypt',
    gender: 'male',
    profile_image: null,
  }
  pmImageFile.value = null
  pmImagePreview.value = null
  pmErrors.value = {}
  pmErrorMsg.value = ''
}

function openPmCreate() {
  resetPmForm()
  showPmModal.value = true
}

function closePmCreate() {
  showPmModal.value = false
}

function onPmImage(e) {
  const f = e.target.files?.[0] || null
  pmImageFile.value = f
  pmForm.value.profile_image = f
  pmImagePreview.value = f ? URL.createObjectURL(f) : null
}

function setPmFieldError(field, msgs) {
  if (!msgs || (Array.isArray(msgs) && msgs.length === 0)) {
    delete pmErrors.value[field]; return
  }
  pmErrors.value[field] = Array.isArray(msgs) ? msgs : [String(msgs)]
}

function validatePm() {
  pmErrors.value = {}
  pmErrorMsg.value = ''
  const f = pmForm.value
  const need = (v) => v !== null && v !== undefined && String(v).trim() !== ''

  if (!need(f.name)) setPmFieldError('name', 'Name is required')
  if (!need(f.email)) setPmFieldError('email', 'Email is required')
  else if (!/^\S+@\S+\.\S+$/.test(f.email)) setPmFieldError('email', 'Invalid email')
  if (!need(f.phone)) setPmFieldError('phone', 'Phone is required')
  if (!need(f.password)) setPmFieldError('password', 'Password is required')
  else if (f.password.length < 8) setPmFieldError('password', 'Minimum 8 characters')
  if (!need(f.country_code)) setPmFieldError('country_code', 'Country code is required')
  if (!need(f.country_name)) setPmFieldError('country_name', 'Country name is required')
  if (!need(f.gender)) setPmFieldError('gender', 'Gender is required')
  if (!f.profile_image) setPmFieldError('profile_image', 'Image is required')

  if (Object.keys(pmErrors.value).length) {
    pmErrorMsg.value = 'Please fix the highlighted fields.'
    return false
  }
  return true
}

async function submitPmCreate() {
  if (!validatePm()) return
  savingPm.value = true
  try {
    const fd = buildVendorCreateFD({ ...pmForm.value, profile_image: pmImageFile.value, is_enabled: true })
    await VendorsApi.create(fd)

    const emailJustCreated = pmForm.value.email
    const vendors = await VendorsApi.list()
    vendorOptions.value = vendors.map(v => ({ id: v.id, name: v.name, email: v.email }))

    const justCreated = vendorOptions.value.find(v => v.email === emailJustCreated)
    if (justCreated) form.value.vendor_id = justCreated.id

    closePmCreate()
  } catch (e) {
    show(e)
    const status = e?.response?.status
    const errs = e?.response?.data?.errors
    if (status === 422 && errs) {
      for (const [k, arr] of Object.entries(errs)) setPmFieldError(k, arr)
      pmErrorMsg.value = e?.response?.data?.message || 'Validation failed.'
    } else {
      pmErrorMsg.value = e?.response?.data?.message || e?.message || 'Request failed'
    }
  } finally {
    savingPm.value = false
  }
}

onMounted(async () => {
  await Promise.all([fetchUnits(), fetchOptions()])
})
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500;
}

.back-drop {
  margin-top: -25px;
}

.modal-top {
  margin-top: -25px !important;
}

.nav-btn {
  @apply flex items-center justify-center gap-1 px-3 py-2 border rounded-lg text-sm text-gray-700 bg-gray-50 hover:bg-gray-100 hover:shadow transition;
}
</style>
