<x-app-layout>

    <div class="container mx-auto px-4 py-8">
        <!-- Page Heading -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">✏️ Update Blog</h2>
            <a href="blog-list.html"
                class="px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg shadow-md hover:bg-gray-700 transition">
                ← Back to Blogs
            </a>
        </div>

        <!-- Form Container -->
        <div class="bg-white shadow-lg rounded-lg p-6 max-w-2xl mx-auto">
            <form action="#" method="post" enctype="multipart/form-data">
                <!-- Title Field -->
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title:</label>
                    <input type="text" id="title" name="title" value="Sample Blog Title"
                        class="mt-1 p-3 w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
                        placeholder="Enter title">
                </div>

                <!-- Description Field -->
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                    <textarea id="description" name="description" rows="4"
                        class="mt-1 p-3 w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
                        placeholder="Enter description">This is a sample blog description.</textarea>
                </div>

                <!-- Banner Image Preview -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Current Banner Image:</label>
                    <img src="banner-image.jpg" alt="Banner Image"
                        class="w-full h-48 object-cover rounded-lg shadow-md">
                </div>

                <!-- Banner Image Upload -->
                <div class="mb-4">
                    <label for="banner_image" class="block text-sm font-medium text-gray-700">Upload New Banner
                        Image:</label>
                    <input type="file" id="banner_image" name="banner_image"
                        class="mt-1 p-2 w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit"
                        class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold shadow-md hover:bg-green-700 transition">
                        ✅ Update Blog
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
