<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>All Packages – HindSafar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <header class="bg-white shadow mb-6">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold">Explore Packages</h1>
      <a href="./dashboard.php" class="text-blue-600 hover:underline">Back to Dashboard</a>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4" id="package-container">
    <div class="text-center py-10 text-gray-500">Loading packages...</div>
  </main>

  <script>
    async function fetchPackages() {
      const container = document.getElementById('package-container');
      try {
        const res = await fetch('./backend/api/packages/get-list.php'); // 🔁 your real API endpoint here
        const data = await res.json();

        container.innerHTML = '';

        for (const [category, packages] of Object.entries(data)) {
          const section = document.createElement('section');
          section.className = "mb-10";
          section.innerHTML = `
            <h2 class="text-xl font-semibold mb-3 capitalize">${category}</h2>
            <div class="flex overflow-x-auto space-x-4 pb-2">
              ${packages.map(pkg => renderPackageCard(pkg)).join('')}
            </div>
          `;
          container.appendChild(section);
        }
      } catch (err) {
        console.error(err);
        container.innerHTML = '<div class="text-red-500 text-center">Failed to load packages.</div>';
      }
    }

    function renderPackageCard(pkg) {
      const banner = (pkg.banner && pkg.banner !== "null") 
        ? pkg.banner 
        : "https://via.placeholder.com/300x160?text=No+Banner";

      const includes = [];
      if (pkg.flights_included === "1") includes.push("✈️ Flights");
      if (pkg.meals_included === "1") includes.push("🍽️ Meals");
      if (pkg.accommodation_included === "1") includes.push("🏨 Stay");
      if (pkg.location_commute_included === "1") includes.push("🚌 Commute");

      return `
        <div class="bg-white cursor-pointer rounded-xl shadow min-w-[280px] max-w-xs overflow-hidden hover:shadow-lg transition" onclick="showpackage('${pkg.package_id}')">
          <img src="${banner}" alt="Package Banner" class="w-full h-40 object-cover">
          <div class="p-4">
            <h3 class="text-lg font-semibold">${pkg.package_name}</h3>
            <p class="text-blue-600 font-semibold mt-1">₹${parseInt(pkg.price).toLocaleString()}</p>
            <ul class="text-sm text-gray-600 mt-2 space-y-1">
              ${includes.map(item => `<li>${item}</li>`).join('')}
            </ul>
          </div>
        </div>
      `;
    }

    function showpackage(id){
      console.log("Id " + id);
      window.location.href = "./show-packages.php?id="+id;
    }
    fetchPackages();
  </script>
</body>
</html>
