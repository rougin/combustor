{% for script in scripts %}
    <script type="text/javascript" src="{{ script | raw }}"></script>
{% endfor %}
    <?php if ($this->session->flashdata('notification')): ?>
        <script type="text/javascript">
            alert('<?php echo $this->session->flashdata("notification"); ?>')
        </script>
    <?php endif; ?>
</body>
</html>