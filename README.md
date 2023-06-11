## Requirements
 - docker
---
## Usage

Run the following command to build the docker image:

```./local.sh start```

---
Run the following command to run migrations:

```./local.sh migrate```

Run the following command to run seeds:

```./local.sh seed``` **It will create 5kk users!!!**

Run the following command to run tests:

```./local.sh ssh``` and then ```php index.php```