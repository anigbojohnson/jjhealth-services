document.addEventListener("DOMContentLoaded", function () {

  const steps = [
    {
      step: "Step 01",
      title: "Inquire",
      text: "Once you apply here, we’ll schedule a consultation to discuss your current situation, your goals, and the details of your ideal partnership with a dating and image consultant."
    },
    {
      step: "Step 02",
      title: "Consult",
      text: "We’ll walk through your style, preferences, and relationship goals during a personalized consultation session."
    },
    {
      step: "Step 03",
      title: "Match & Style",
      text: "Together we’ll develop your personal brand and initiate curated matchmaking tailored just for you."
    }
  ];

  let currentStep = 0;

  const stepContent = document.getElementById("step-content");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const stepNav = document.getElementById("stepNav");

  function renderStep() {

    const content = steps[currentStep];

    stepContent.innerHTML = `
      <p class="text-uppercase text-muted small fw-semibold mb-1">${content.step}</p>
      <h3 class="fw-bold mb-3">${content.title}</h3>
      <p class="text-secondary">${content.text}</p>
    `;

    // Handle previous button visibility
    if (currentStep === 0) {
      prevBtn.style.display = "none";
      stepNav.classList.remove("justify-content-between");
      stepNav.classList.add("justify-content-end");
    } else {
      prevBtn.style.display = "inline-block";
      stepNav.classList.remove("justify-content-end");
      stepNav.classList.add("justify-content-between");
    }

    // Change next button text
    nextBtn.innerHTML =
      currentStep < steps.length - 1 ? "Next &rarr;" : "Book now";
  }

  window.goToNext = function () {
    if (currentStep < steps.length - 1) {
      currentStep++;
      renderStep();
    } else {
      alert("You're done!");
    }
  };

  window.goToPrevious = function () {
    if (currentStep > 0) {
      currentStep--;
      renderStep();
    }
  };

  renderStep();

});
