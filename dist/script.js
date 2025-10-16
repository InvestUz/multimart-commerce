const shadow = document.getElementById("shadow");
const card = document.getElementById("card");

document.body.addEventListener("mousemove", (e) => {
  const { clientX, clientY } = e;
  if (e.target.closest("#card")) {
    shadow.style.setProperty(
      "transform",
      `translateX(${clientX - 60}px) translateY(${clientY - 60}px)`
    );
    shadow.style.setProperty(
      "opacity",
      "0.5"
    );
  } else {
    shadow.style.setProperty("opacity", "0");
  }
});