<!-- partial:partials/_footer.html -->
<footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            
           
          </div>
        </footer>
<!-- partial -->

  <!-- Custom js for this page-->

  <script src="{{ config('myconfig.config.server_url')  }}js/_my_page_public_{!! Request::path()=='/' ? 'home' : Request::path() !!}.js"></script>

  <!-- End custom js for this page-->

</body>
</html>