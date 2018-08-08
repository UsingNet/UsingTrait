UsingTrait
--------------

UsingTrait 是一个基于 Trait 实现的应用开发框架。 


### 约定
* 所有表名和字段名使用下划线结构
* Model使用驼峰结构
* 表内自增ID字段为 id
* 外键使用 表名 + _id
* 当有多个外键指向相同表时，使用  prefix_ + 表名 + _id

