// Render the OpenAPI spec on any page with a #swagger-ui element. Uses document$
// so it also runs after instant navigation.
document$.subscribe(function () {
  var el = document.getElementById("swagger-ui");
  if (!el || typeof SwaggerUIBundle === "undefined") return;

  SwaggerUIBundle({
    domNode: el,
    url: new URL(el.dataset.spec, window.location.href).href,
    deepLinking: true,
    defaultModelsExpandDepth: 0,
    tryItOutEnabled: false,
    supportedSubmitMethods: [],
  });
});
