/* =========================================================
   ATWOPAT - MASTER FRONTEND SCRIPT (verify.js)
   Updated: May 2026 
   Features: Direct DB Hosting, Frosted UI, & Success Animation
   ========================================================= */

async function verifyMember(idOverride) {
  let id = idOverride || document.getElementById("memberID").value.trim();

  if (!id) {
    alert("Please enter a Member ID");
    return;
  }

  const resultDiv = document.getElementById("resultContainer");
  const successModal = document.getElementById("success-modal");

  // Loading State
  resultDiv.innerHTML = `
    <div style="text-align:center; padding:20px;">
      <div class="spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #103d75; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; margin: auto;"></div>
      <p style="color:#103d75; font-weight:bold; margin-top:15px;">Searching Database...🤸</p>
    </div>
    <style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>
  `;

  try {
    // 1. Updated fetch to point to your new PHP API
    let response = await fetch(`api/verify.php?id=${encodeURIComponent(id)}`);
    let data = await response.json();

    if (!data || data.status === "error" || data.status === "NOT_FOUND") {
      resultDiv.innerHTML = `
        <div style="background:white; padding:20px; border-radius:15px; text-align:center; color:#d9534f; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-top: 20px;">
          ❌ <b>Member Not Found</b><br>Please check the ID and try again.
        </div>`;
      return;
    }

    // 2. Trigger Success Animation Modal
    successModal.style.display = 'flex';

    // 3. Prepare Member Data from your Database Structure
    const member = {
      id: data.member.member_id || 'N/A',
      name: data.member.full_name || 'Unknown',
      state: data.member.state || 'N/A',
      role: data.member.position || 'Member',
      statusValue: data.member.status || 'Inactive',
      // Photos now load from your local images folder
      photo: data.member.photo ? `images/members/${data.member.photo}` : 'images/members/default-avatar.png',
      expiry: data.member.expiry_date || 'N/A'
    };

    let currentStatus = (member.statusValue).toString().trim().toLowerCase();
    let nameBadge = ""; 
    let statusDisplay = "";
    let actionButton = "";

    // Handle Active Status
    if (currentStatus === "active") {
      nameBadge = `<img src="images/verify-badge.png" style="width:20px; height:20px; margin-left:8px;" title="Verified">`;
      statusDisplay = `<span style="color:#22c55e; font-weight:bold;">Active</span>`;
      actionButton = `
        <a href="https://t.me/+0qCgEbssFKw3ZmM0" target="_blank" style="text-decoration:none;">
          <button style="background:#103d75; color:white; width:100%; padding:14px; border:none; border-radius:12px; font-weight:bold; cursor:pointer; margin-top:15px; transition: 0.3s; box-shadow: 0 4px 10px rgba(16,61,117,0.2);">
            Join Official Telegram
          </button>
        </a>`;
    } else {
      statusDisplay = `<span style="color:#d9534f; font-weight:bold;">${member.statusValue}</span>`;
    }

    // 4. Delay showing the card until the checkmark animation finishes
    setTimeout(() => {
      successModal.style.display = 'none';

      /* FINAL UI OUTPUT (Frosted Glass Card) */
      resultDiv.innerHTML = `
        <div class="card animate-fade-in" style="
          background: rgba(255, 255, 255, 0.7); 
          backdrop-filter: blur(15px); 
          -webkit-backdrop-filter: blur(15px);
          border: 1px solid rgba(255, 255, 255, 0.3); 
          padding: 25px; 
          border-radius: 25px; 
          max-width: 380px; 
          margin: 20px auto; 
          text-align: center;
          box-shadow: 12px 12px 24px rgba(0,0,0,0.05), -12px -12px 24px rgba(255,255,255,0.8);
          font-family: sans-serif;">
          
          <h3 style="margin: 0 0 15px 0; color:#103d75; letter-spacing:1px; font-size: 18px;">ATWOPAT OFFICIAL ID</h3>
          
          <div style="width: 140px; height: 140px; margin: 0 auto 20px; border-radius: 20px; overflow: hidden; border: 4px solid white; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
            <img src="${member.photo}" 
                 style="width: 100%; height: 100%; object-fit: cover;" 
                 onerror="this.src='images/members/default-avatar.png';">
          </div>

          <div style="text-align: left; color: #103d75; font-size: 15px; line-height: 2;">
            <div style="border-bottom: 1px solid rgba(16,61,117,0.05); padding: 5px 0; display: flex; align-items: center;">
              <b style="width: 100px;">Full Name:</b> 
              <span style="font-weight: 700;">${member.name}${nameBadge}</span>
            </div>
            <div style="border-bottom: 1px solid rgba(16,61,117,0.05); padding: 5px 0;">
              <b style="width: 100px; display: inline-block;">State:</b> <span>${member.state}</span>
            </div>
            <div style="border-bottom: 1px solid rgba(16,61,117,0.05); padding: 5px 0;">
              <b style="width: 100px; display: inline-block;">Position:</b> <span>${member.role}</span>
            </div>
            <div style="border-bottom: 1px solid rgba(16,61,117,0.05); padding: 5px 0;">
              <b style="width: 100px; display: inline-block;">Member ID:</b> <span style="font-family: monospace; font-weight: bold;">${member.id}</span>
            </div>
            <div style="border-bottom: 1px solid rgba(16,61,117,0.05); padding: 5px 0;">
              <b style="width: 100px; display: inline-block;">Status:</b> ${statusDisplay}
            </div>
            <div style="padding: 5px 0;">
              <b style="width: 100px; display: inline-block;">Expiry:</b> <span>${member.expiry}</span>
            </div>
          </div>

          ${actionButton}
        </div>
      `;
    }, 2500);

  } catch (error) {
    console.error("Verification Error:", error);
    resultDiv.innerHTML = `
      <div style="background:white; padding:15px; border-radius:10px; color:#d9534f; margin-top:20px;">
        ❌ <b>Network Error</b><br>Could not connect to the database.
      </div>`;
  }
}
