<?php
    error_reporting(E_ALL);
    $productType = $_GET['product_type'];
    $brand = $_GET['brand'];
    $file = $_GET['file'];
    include 'converter.php';
    include 'header.php';
    
?>
<div class="container">
    <div class="col-xs-12">
        
        <?php if(!$productType): ?>
        <div class="form-group">
            <form method="get" action="">
                <h1><i class="fa fa-cogs"></i> Step 1</h1>
                <h3 class="page-header">1. Select your product type</h1>
                <select class="form-control" name="product_type" id="product_type">
                    <option value="rug">Rug</option>
                    <option value="furniture">Furniture</option>
                </select>
                <h3 class="page-header">2. Select your brand</h1>
                <select class="form-control" name="brand" id="brand">
                    <option value="feizy">Feizy</option>
                    <option value="rizzy">Rizzy</option>
                </select>
                <br/>
                <button type="submit" class="btn btn-lg btn-primary">Go to Step 2 <i class="fa fa-chevron-right"></i></button>
            </form>
        </div>
        <?php elseif(!$file): ?>
        <div class="form-group">
            <form method="get" action="">
                <h1><i class="fa fa-cogs"></i> Step 2</h1>
                <h3 class="page-header">1. Select the file to convert</h1>
                <p class="bg-primary" style="padding: 10px"><i class="fa fa-upload"></i> Upload tour files by FTP in the "in/data/" folder.</p>
                <select class="form-control" name="file" id="file">
                    <?php
                        foreach(glob('in/data/*') as $file):
                    ?>
                        <option value="<?php echo $file; ?>"><?php echo $file; ?></option>
                    <?php endforeach; ?>
                </select>
                <br/>
                <input type="hidden" name="product_type" value="<?php echo $productType; ?>" />
                <input type="hidden" name="brand" value="<?php echo $brand; ?>" />
                <button type="submit" class="btn btn-lg btn-primary"><i class="fa fa-check-circle"></i> Convert</button>
            </form>
        </div>
        <?php else: ?>
        <div class="form-group">
            <h1><i class="fa fa-cogs"></i> Step 3</h1>
            <br/>
            <p class="bg-primary" style="padding: 10px"><i class="fa fa-download"></i> Download your MAGMI file, or find it under "out" folder</p>
            <br/>
            <button type="button" class="btn btn-lg btn-primary" onclick="window.location='<?php echo addslashes($fileName); ?>';"><i class="fa fa-download"></i> Download</button>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
    include 'footer.php';
?>

